<?php

namespace App\Jobs;

use App\Collections\UserCollection;
use App\DTOs\UserDto;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\Email;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class EmailChannelAdministratorsPromoStatusChangedJob extends Job
{
    private $promotion;

    public function __construct(
        Promotion $promotion
    ) {
        $this->promotion = $promotion;
    }

    public function handle(
        ChannelServiceInterface $channelService,
        UserServiceInterface $userService,
        Queue $queue
    ): void {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');

        $channelId = $this->promotion->getChannelId();
        $promotionId = $this->promotion->getId();
        $promotionStatus = $this->promotion->getStatus();

        $newsletter = $channelService->getChannelById($channelId);

        if (empty($newsletter)) {
            return;
        }

        $newsletterName = $newsletter->getTitle();

        $brandAdministrators = $userService->getBrandAdministrators($newsletter->getBrandId());
        $emailCopy = $this->createPromoStatusCopy($newsletterName, $promotionStatus);

        $promotionUrl = env('SERVICE_ARAGORN_URL');

        $callToAction = 'Review Promotion';
        $callToActionUrl = "{$promotionUrl}/promotions/{$promotionId}";

        $heading = $this->createPromoStatusHeading($newsletterName, $promotionStatus);

        $administratorEmailsArray = array_map(
            function ($brandAdministrator) use (
                $callToAction,
                $callToActionUrl,
                $emailCopy,
                $fromEmail,
                $heading,
                $newsletterName,
                $promotionId,
                $queue
            ) {
                $administratorEmail = $brandAdministrator->getEmail();
                $administratorName = $brandAdministrator->getName();

                $copy = "
                    <p>
                        Hi {$administratorName},
                    </p>
                    <p>
                        {$emailCopy}
                    </p>
                    <table>
                        <tbody>
                            <tr>
                                <td style='font-weight: bold;'>Newsletter:</td>
                                <td>{$newsletterName}</td>
                            </tr>
                            <tr>
                                <td style='font-weight: bold; text-align: right;'>#</td>
                                <td><a href='{$callToActionUrl}'>{$promotionId}</a></td>
                            </tr>
                        </tbody>
                    </table>
                ";

                $email = new Email();
                $email->setContent($copy);
                $email->setFromEmail($fromEmail);
                $email->setSubject($heading);

                $emailJob = new SendEmailJob(
                    $callToAction,
                    $callToActionUrl,
                    $email,
                    $administratorEmail
                );

                $queue->pushOn('send_email', $emailJob);
            },
            $brandAdministrators->toArray()
        );
    }

    private function createPromoStatusHeading(string $channelName, int $promotionStatus): string
    {
        switch ($promotionStatus) {
            case Promotion::STATUS_NEWLY_CREATED:
                return "A promotion in ${channelName} was just created.";
                break;
            case Promotion::STATUS_PUBLICATION_IN_PROGRESS:
                return "Publication of a promotion in ${channelName} is in progress.";
                break;
            case Promotion::STATUS_PUBLISHED:
                return "A promotion in ${channelName} was just published .";
                break;
            case Promotion::STATUS_PENDING_APPROVAL_FROM_PUBLISHER:
                return "A promotion in {$channelName} is pending review.";
                break;
            case Promotion::STATUS_APPROVED_FOR_PUBLICATION:
                return "A promotion in {$channelName} has been approved.";
                break;
            default:
                return "The status of a promotion in {$channelName} has changed.";
                break;
        }
    }

    private function createPromoStatusCopy(string $channelName, int $promotionStatus): string
    {
        switch ($promotionStatus) {
            case Promotion::STATUS_NEWLY_CREATED:
                return "This promotion, slated for placement in <b>${channelName}</b>, was <b>just created.</b>";
                break;
            case Promotion::STATUS_PUBLICATION_IN_PROGRESS:
                return "Publication of this promotion in <b>${channelName}</b> is <b>in progress</b>.";
                break;
            case Promotion::STATUS_PUBLISHED:
                return "This promotion placed in <b>${channelName}</b> was just <b>published</b>.";
                break;
            case Promotion::STATUS_PENDING_APPROVAL_FROM_PUBLISHER:
                return "We're just confirming a new promotion was placed in <b>{$channelName}</b> and is <b>pending review</b>. Congrats!";
                break;
            case Promotion::STATUS_APPROVED_FOR_PUBLICATION:
                return "This promotion placed in <b>{$channelName}</b> has been <b>approved for publication</b>.";
                break;
            default:
                return "It appears the status of this promotion placed in <b>{$channelName}</b> has recently changed and <b>requires your attention.</b>";
                break;
        }
    }
}
