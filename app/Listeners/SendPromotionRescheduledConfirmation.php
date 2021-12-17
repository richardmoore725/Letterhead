<?php

namespace App\Listeners;

use App\Events\PromotionRescheduledEvent;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Email;
use App\Models\Promotion;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\SendEmailJob;
use App\Jobs\EmailChannelAdministratorsPromoIsRescheduledJob;
use Carbon\CarbonImmutable;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

/**
 * This class is responsible for emailing folks a confirmation that a promotion has been scheduled.
 * Class SendPromotionScheduledConfirmation
 * @package App\Listeners
 */
class SendPromotionRescheduledConfirmation implements ShouldQueue
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

    /**
     * @param PromotionRescheduledEvent $event
     */
    public function handle(PromotionRescheduledEvent $event)
    {
                /**
         * @var Promotion
         */
        $promotion = $event->promotion;
        $promotionId = $promotion->getId();

        /**
         * @var int
         */
        $userId = $event->userId;

        $newsletter = $this->channelService->getChannelById($promotion->getChannelId());

        if (empty($newsletter)) {
            Rollbar::log(
                Level::WARNING,
                "A promotion was published with a channel Id we could not find.",
                [
                    'promotionId' => $promotionId,
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
                    'promotionId' => $promotionId,
                    'userId' => $userId,
                ]
            );

            return;
        }

        $promotionUrl = env('SERVICE_ADS_URL', 'https://store.tryletterhead.com');
        $callToAction = 'Edit promotion';
        $callToActionUrl = "{$promotionUrl}/account/promotions/{$promotionId}";
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');
        $newsletterName = $newsletter->getTitle();
        $publicationBuffer = $newsletter->getAdSchedulingBuffer();
        $publicationDate = $promotion->getDateStart();
        $toEmail = $user->getEmail();
        $userName = $user->getName();

        /**
         * Let's transform our `YYYY-MM-DD` date to something nicer, like "January 23rd, 2012."
         */
        $friendlyPublicationDate = CarbonImmutable::parse($publicationDate)->format('F jS, Y');
        $heading = "Your promotion #{$promotionId} in {$newsletterName} has been rescheduled";

        $copy = "
            <p>
                Hi {$userName},
            </p>

            <p>
                We are just confirming you rescheduled your promotion #{$promotionId} in {$newsletterName}
                to <b>{$friendlyPublicationDate}</b>. You can <a href='{$callToActionUrl}'>reschedule
                or make changes</a> up to {$publicationBuffer} hours before then.
            </p>

            <table>
                <tbody>
                    <tr>
                        <td style='font-weight: bold;'>#</td>
                        <td><a href='{$callToActionUrl}'>{$promotionId}</a></td>
                    </tr>

                    <tr>
                        <td style='font-weight: bold;'>Newsletter</td>
                        <td>{$newsletterName}</td>
                    </tr>
                    
                    <tr>
                        <td style='font-weight:bold;'>Scheduled</td>
                        <td>{$friendlyPublicationDate} (<small><a href='{$callToActionUrl}'>reschedule</a></small>)</td>
                    </tr>
                </tbody>
            </table>            
        ";

        $email = new Email();
        $email->setContent($copy);
        $email->setFromEmail($fromEmail);
        $email->setSubject($heading);

        $emailJob = new SendEmailJob($callToAction, $callToActionUrl, $email, $toEmail);

        $emailChannelAdministratorsJob = new EmailChannelAdministratorsPromoIsRescheduledJob(
            $friendlyPublicationDate,
            $fromEmail,
            $promotionId,
            $userName,
            $newsletter
        );

        $this->queue->pushOn('send_email', $emailJob);

        $this->queue->pushOn('send_email', $emailChannelAdministratorsJob);
    }
}
