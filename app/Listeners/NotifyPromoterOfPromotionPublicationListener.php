<?php

namespace App\Listeners;

use App\Events\PromotionPublishedEvent;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Email;
use App\Models\Promotion;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\SendEmailJob;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

/**
 * This listener is responsible for responding to a PromotionPublished event and emailing a
 * notification to the user as well as to our sales time.
 *
 * Class NotifyPromoterOfPromotionPublicationListener
 * @package App\Listeners
 */
class NotifyPromoterOfPromotionPublicationListener implements ShouldQueue
{
    /**
     * @var BeaconServiceInterface
     */
    private $beaconService;

    /**
     * @var ChannelServiceInterface
     */
    private $channelService;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        BeaconServiceInterface $beaconService,
        ChannelServiceInterface $channelService,
        Queue $queue,
        UserServiceInterface $userService
    ) {
        $this->beaconService = $beaconService;
        $this->channelService = $channelService;
        $this->queue = $queue;
        $this->userService = $userService;
    }

    public function handle(PromotionPublishedEvent $event)
    {
        /**
         * @var Promotion
         */
        $promotion = $event->promotion;

        /**
         * @var int
         */
        $userId = $event->userId;

        $channel = $this->channelService->getChannelById($promotion->getChannelId());

        if (empty($channel)) {
            Rollbar::log(
                Level::WARNING,
                "A promotion was published with a channel Id we could not find.",
                [
                    'promotionId' => $promotion->getId(),
                    'channelId' => $promotion->getChannelId(),
                ]
            );

            return;
        }

        $user = $this->userService->getUserById($userId);

        if (empty($user)) {
            Rollbar::log(
                Level::WARNING,
                "A promotion was published by a user we couldn't find.",
                [
                    'promotionId' => $promotion->getId(),
                    'userId' => $userId,
                ]
            );

            return;
        }

        $emailCopy = $this->getEmailCopy($promotion, $channel->getTitle());

        if (empty($emailCopy)) {
            return;
        }

        $letterheadEndpoint = env('SERVICE_ARAGORN_URL', 'https://app.tryletterhead.com');

        $callToAction = "View your metrics";
        $callToActionUrl = "{$letterheadEndpoint}/account/orders";

        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');
        $heading = "Your promotion was published in {$channel->getTitle()}";

        $email = new Email();
        $email->setContent($emailCopy);
        $email->setFromEmail($fromEmail);
        $email->setSubject($heading);

        $emailJob = new SendEmailJob($callToAction, $callToActionUrl, $email, $user->getEmail());

        $this->queue->pushOn('send_email', $emailJob);
    }

    private function getEmailCopy(Promotion $promotion, string $channelName): string
    {
        $arrayOfPromotionPropertiesToEmail = [
            'Heading' => $promotion->getHeading(),
            'Emoji' => $promotion->getEmoji(),
            'Copy' => $promotion->getBlurb(),
            'Published' => $promotion->getDateStart(),
            'Promotion #' => $promotion->getId(),
        ];

        return $this->renderEmailBladeTemplateAsString($arrayOfPromotionPropertiesToEmail, $channelName);
    }

    private function renderEmailBladeTemplateAsString(array $arrayOfPromotionPropertiesToEmail, string $channelName): string
    {
        try {
            $arrayOfTemplateVariables = [
                'arrayOfPromotionPropertiesToEmail' => $arrayOfPromotionPropertiesToEmail,
                'channelName' => $channelName,
            ];
            return view('emails.promotion-published-notification', $arrayOfTemplateVariables)->render();
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return '';
        }
    }
}
