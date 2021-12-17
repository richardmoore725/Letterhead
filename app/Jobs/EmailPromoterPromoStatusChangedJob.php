<?php

namespace App\Jobs;

use App\Collections\UserCollection;
use App\DTOs\UserDto;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\Email;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class EmailPromoterPromoStatusChangedJob extends Job
{
    private $promotion;

    public function __construct(
        Promotion $promotion
    ) {
        $this->promotion = $promotion;
    }

    public function handle(
        AdServiceInterface $adService,
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

        $emailCopy = $this->createPromoStatusCopy($newsletterName, $promotionStatus);

        $promotionUrl = env('SERVICE_ARAGORN_URL');

        $promotionCredit = $adService->getPromotionCreditByPromotionId($promotionId);

        $promoter = $userService->getUserById($promotionCredit->getUserId());
        $promoterEmail = $promoter->getEmail();

        $callToAction = 'Review Promotion';
        $callToActionUrl = "{$promotionUrl}/promotions/{$promotionId}";

        $heading = $this->createPromoStatusHeading($newsletterName, $promotionStatus);

        $copy = "
            <p>
                Hi there,
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
            $promoterEmail
        );

        $queue->pushOn('send_email', $emailJob);
    }

    private function createPromoStatusHeading(string $channelName, int $promotionStatus): string
    {
        switch ($promotionStatus) {
            case Promotion::STATUS_NEWLY_CREATED:
                return "Your promotion in ${channelName} was just created.";
                break;
            case Promotion::STATUS_PUBLICATION_IN_PROGRESS:
                return "Publication of your promotion in ${channelName} is in progress.";
                break;
            case Promotion::STATUS_PUBLISHED:
                return "Your promotion in ${channelName} was just published .";
                break;
            case Promotion::STATUS_PENDING_APPROVAL_FROM_PUBLISHER:
                return "Your promotion in {$channelName} is pending review.";
                break;
            case Promotion::STATUS_APPROVED_FOR_PUBLICATION:
                return "Your promotion in {$channelName} has been approved.";
                break;
            default:
                return "The status of your promotion in {$channelName} has changed.";
                break;
        }
    }

    private function createPromoStatusCopy(string $channelName, int $promotionStatus): string
    {
        switch ($promotionStatus) {
            case Promotion::STATUS_NEWLY_CREATED:
                return "Your promotion, slated for placement in <b>${channelName}</b>, was <b>just created</b>. You can edit it at the link below.";
                break;
            case Promotion::STATUS_PUBLICATION_IN_PROGRESS:
                return "We're just letting you know that publication of your promotion in <b>${channelName}</b> is <b>in progress</b>.";
                break;
            case Promotion::STATUS_PUBLISHED:
                return "Your promotion in <b>${channelName}</b> was just <b>published</b>. You should see it it in the latest edition!";
                break;
            case Promotion::STATUS_PENDING_APPROVAL_FROM_PUBLISHER:
                return "Your new promotion in <b>{$channelName}</b> is now <b>pending review</b>. Keep an eye out for further updates.";
                break;
            case Promotion::STATUS_APPROVED_FOR_PUBLICATION:
                return "Your promotion in <b>{$channelName}</b> has been <b>approved for publication</b>.";
                break;
            default:
                return "It appears the status of your promotion in <b>{$channelName}</b> has recently changed and <b>requires your attention.</b>";
                break;
        }
    }
}
