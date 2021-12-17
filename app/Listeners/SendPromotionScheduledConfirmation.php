<?php

namespace App\Listeners;

use App\Events\PromotionScheduledEvent;
use App\Jobs\SendEmailJob;
use App\Jobs\EmailChannelAdministratorsJob;
use App\Models\Email;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * This class is responsible for emailing folks a confirmation that a promotion has been scheduled.
 * Class SendPromotionScheduledConfirmation
 * @package App\Listeners
 */
class SendPromotionScheduledConfirmation implements ShouldQueue
{
    private $queue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        Queue $queue
    ) {
        $this->queue = $queue;
    }

    /**
     * @param PromotionScheduledEvent $event
     */
    public function handle(PromotionScheduledEvent $event)
    {
        $promotionUrl = env('SERVICE_ADS_URL', 'https://store.tryletterhead.com');
        $newsletter = $event->newsletter;
        $passport = $event->passport;
        $promotion = $event->promotion;

        $callToAction = 'Edit promotion';
        $callToActionUrl = "{$promotionUrl}/account/promotions/{$promotion->id}";
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');
        $newsletterName = $newsletter->getTitle();
        $promotionId = $promotion->id;
        $publicationBuffer = $newsletter->getAdSchedulingBuffer();
        $publicationDate = $promotion->dateStart;
        $toEmail = $passport->getEmail();
        $userName = $passport->getName();

        /**
         * Let's transform our `YYYY-MM-DD` date to something nicer, like "January 23rd, 2012."
         */
        $friendlyPublicationDate = CarbonImmutable::parse($publicationDate)->format('F jS, Y');
        $heading = "Your promotion in {$newsletterName} has been scheduled";

        $copy = "
            <p>
                Hi {$userName},
            </p>

            <p>
                We are just confirming you placed your promotion in {$newsletterName}
                on <b>{$friendlyPublicationDate}</b>. You can <a href='{$callToActionUrl}'>reschedule
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

        $emailChannelAdministratorsJob = new EmailChannelAdministratorsJob(
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
