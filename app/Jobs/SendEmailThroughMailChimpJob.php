<?php

namespace App\Jobs;

use App\Collections\UserCollection;
use App\Collections\LetterPartCollection;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\LetterServiceInterface;
use App\Http\Services\MailChimpFacade;
use App\Http\Services\UserServiceInterface;
use App\Models\Letter;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Symfony\Component\DomCrawler\Crawler;

class SendEmailThroughMailChimpJob extends Job
{
    /**
     * @var Letter
     */
    private $letter;

    public function __construct(Letter $letter)
    {
        $this->letter = $letter;
    }

    public function handle(
        ChannelServiceInterface $channelService,
        LetterServiceInterface $letterService,
        UserServiceInterface $userService
    ): void {
        $letterToMail = $letterService->getLetterById($this->letter->getId());

        if (empty($letterToMail)) {
            return;
        }

        /**
         * Get the channel
         */
        $channel = $channelService->getChannelById($letterToMail->getChannelId());

        /**
         * We need this :).
         */
        $includePixel = true;

        /**
         * Get the authors of the letter.
         */
        $arrayOfAuthors = $letterToMail->getAuthors();

        /**
         * Generate an array of user Ids from the LetterUsersCollection
         */
        $arrayOfAuthorIds = $arrayOfAuthors->getArrayOfUserIds()->toArray();

        $users = empty($arrayOfAuthorIds) ? new UserCollection() : $userService->getUsersByUserIds($arrayOfAuthorIds);

        $markup = $letterService->generateLetterMarkup($users, $channel, $includePixel, $letterToMail);

        if ($markup->isError()) {
            Rollbar::log(
                Level::WARNING,
                "We failed to mail the newsletter {$letterToMail->getId()}"
            );
            return;
        }

        $mcApiKey = $channel->getChannelConfigurations()->getMcApiKey();

        if (empty($mcApiKey)) {
            Rollbar::log(
                Level::WARNING,
                "Remember to set a valid MailChimp API key for {$channel->getId()}so we can connect"
            );
            return;
        }

        $mailChimp = MailChimpFacade::createFromChannel($channel);

        $response = $mailChimp->sendEmailForChannel(
            $letterToMail,
            $channel,
            $letterToMail->getTitle(),
            $markup->getEndUserMessage()
        );

        $letterToMail->setCampaignId($response->getData());

        // update letter with campainId
        $letterService->updateLetter(
            $letterToMail->getId(),
            $letterToMail->getAuthors()->getPublicArray(),
            $letterToMail->getParts()->getArrayOfModels(),
            $letterToMail
        );

        $wasSent = $response->getStatus();

        if ($wasSent === 200) {
            $letterService->markLetterAsPublished($letterToMail);
        }
    }
}
