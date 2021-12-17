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

class SendLetterThroughEmailServiceProviderJob extends Job
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
        LetterServiceInterface $letterService
    ): void {
        /**
         * We will need to pass the Channel to LetterService::test(), so let's fetch it.
         */
        $channel = $channelService->getChannelById($this->letter->getChannelId());

        if (empty($channel)) {
            $channelErrorMessage = 'Letter publication failed because channel not found';
            Rollbar::log(Level::CRITICAL, 'Letter publication failed because channel not found', ['letter' => $this->letter]);

            throw new \Exception($channelErrorMessage);
        }

        $sendResponse = $letterService->send(
            $channel,
            $this->letter->getEmailServiceProvider(),
            $this->letter,
            $channel->getDefaultFromEmailAddress(),
            $channel->getDefaultEmailFromName(),
            $this->letter->getTitle(),
            $this->letter->getEmailTemplate()
        );

        if ($sendResponse->isError()) {
            $sendResponseErrorMessage = 'Letter emailing and publication failure';
            Rollbar::log(Level::CRITICAL, 'Letter emailing and publication failure', ['error' => $sendResponse->getData(), 'letter' => $this->letter]);
            throw new \Exception($sendResponseErrorMessage);
        }

        $letterService->markLetterAsPublished($this->letter);
    }
}
