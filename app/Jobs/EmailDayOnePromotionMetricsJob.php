<?php

namespace App\Jobs;

use App\DTOs\PromotionDto;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Email;
use App\Models\Promotion;
use Illuminate\Contracts\Queue\Queue;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class EmailDayOnePromotionMetricsJob extends Job
{
    private $promotion;
    private $userId;

    public function __construct(Promotion $promotion, int $userId)
    {
        $this->promotion = $promotion;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        BeaconServiceInterface $beaconService,
        ChannelServiceInterface $channelService,
        Queue $queue,
        UserServiceInterface $userService
    ): void {
        $channel = $channelService->getChannelById($this->promotion->getChannelId());

        if (empty($channel)) {
            Rollbar::log(
                Level::WARNING,
                "A promotion was published with a channel Id we could not find.",
                [
                    'promotionId' => $this->promotion->getId(),
                    'channelId' => $this->promotion->getChannelId(),
                ]
            );

            return;
        }

        $user = $userService->getUserById($this->userId);

        if (empty($user)) {
            Rollbar::log(
                Level::WARNING,
                "A promotion was published by a user we couldn't find.",
                [
                    'promotionId' => $this->promotion->getId(),
                    'userId' => $this->userId,
                ]
            );

            return;
        }

        $promotionFromAdService = $beaconService->getAdResourceByBeaconSlug('ads', "promotions/{$this->promotion->getId()}");
        if (empty($promotionFromAdService)) {
            Rollbar::log(Level::ERROR, "We failed to query a promotion by its id and could not send a metric report");
            return;
        }

        $promotionDto = new PromotionDto(null, $promotionFromAdService);
        $promotionWithMetrics = new Promotion($promotionDto);

        $emailCopy = $this->getEmailCopy($promotionWithMetrics, $channel->getTitle());

        if (empty($emailCopy)) {
            return;
        }

        $letterheadEndpoint = env('SERVICE_ARAGORN_URL', 'https://app.tryletterhead.com');

        $callToAction = "View your metrics";
        $callToActionUrl = "{$letterheadEndpoint}/account/orders";

        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');
        $heading = "Metrics for your {$channel->getTitle()} promotion";

        $email = new Email();
        $email->setContent($emailCopy);
        $email->setFromEmail($fromEmail);
        $email->setSubject($heading);

        $emailJob = new SendEmailJob($callToAction, $callToActionUrl, $email, $user->getEmail());

        $queue->pushOn('send_email', $emailJob);
    }

    private function getEmailCopy(Promotion $promotion, string $channelName): string
    {
        $arrayOfPromotionMetricsPropertiesToEmail = [
            'Verified clicks' => $promotion->getClicks(),
            'Verified views' => $promotion->getViews(),
        ];

        $arrayOfPromotionPropertiesToEmail = [
            'Heading' => $promotion->getHeading(),
            'Emoji' => $promotion->getEmoji(),
            'Copy' => $promotion->getBlurb(),
            'Published' => $promotion->getDateStart(),
            'Promotion #' => $promotion->getId(),
        ];

        return $this->renderEmailBladeTemplateAsString($arrayOfPromotionMetricsPropertiesToEmail, $arrayOfPromotionPropertiesToEmail, $channelName);
    }

    private function renderEmailBladeTemplateAsString(array $arrayOfPromotionMetricsPropertiesToEmail, array $arrayOfPromotionPropertiesToEmail, string $channelName): string
    {
        try {
            $arrayOfTemplateVariables = [
                'arrayOfPromotionMetricsPropertiesToEmail' => $arrayOfPromotionMetricsPropertiesToEmail,
                'arrayOfPromotionPropertiesToEmail' => $arrayOfPromotionPropertiesToEmail,
                'channelName' => $channelName,
            ];
            return view('emails.day-one-promotion-metrics-notification', $arrayOfTemplateVariables)->render();
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return '';
        }
    }
}
