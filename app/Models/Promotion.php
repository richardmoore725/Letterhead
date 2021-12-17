<?php

namespace App\Models;

use App\DTOs\PromotionDto;

class Promotion
{
    public const STATUS_CHANGES_REQUESTED = 5;
    public const STATUS_APPROVED_FOR_PUBLICATION = 4;
    public const STATUS_NEWLY_CREATED = 0;
    public const STATUS_PENDING_APPROVAL_FROM_PUBLISHER = 3;
    public const STATUS_PUBLICATION_IN_PROGRESS = 1;
    public const STATUS_PUBLISHED = 2;

    private $adTypeId;
    private $advertiserLogo;
    private $blurb;
    private $brandId;
    private $callToAction;
    private $callToActionUrl;
    private $channelId;
    private $content;
    private $dateStart;
    private $emoji;
    private $heading;
    private $id;
    private $markup;
    private $metrics;
    private $mjml;
    private $pixel;
    private $positioning;
    private $promoterDisplayName;
    private $promoterImage;
    private $promoterImageAlternativeText;
    private $resolvedCallToActionUrl;
    private $uniqueId;
    private $scheduledDate;
    private $status;

    public function __construct(PromotionDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->adTypeId = $dto->adTypeId;
        $this->advertiserLogo = $dto->advertiserLogo;
        $this->blurb = $dto->blurb;
        $this->brandId = $dto->brandId;
        $this->callToAction = $dto->callToAction;
        $this->callToActionUrl = $dto->callToActionUrl;
        $this->channelId = $dto->channelId;
        $this->content = $dto->content;
        $this->dateStart = $dto->dateStart;
        $this->emoji = $dto->emoji;
        $this->heading = $dto->heading;
        $this->id = $dto->id;
        $this->markup = $dto->markup;
        $this->metrics = $dto->metrics;
        $this->mjml = $dto->mjml;
        $this->pixel = $dto->pixel;
        $this->positioning = $dto->positioning;
        $this->promoterDisplayName = $dto->promoterDisplayName;
        $this->promoterImage = $dto->promoterImage;
        $this->promoterImageAlternativeText = $dto->promoterImageAlternativeText;
        $this->resolvedCallToActionUrl = $dto->resolvedCallToActionUrl;
        $this->uniqueId = $dto->uniqueId;
        $this->scheduledDate = $dto->scheduledDate;
        $this->status = $dto->status;
    }

    public function convertToArray(): array
    {
        return [
            'adTypeId' => $this->adTypeId,
            'advertiserLogo' => $this->advertiserLogo,
            'blurb' => $this->blurb,
            'brandId' => $this->brandId,
            'callToAction' => $this->callToAction,
            'callToActionUrl' => $this->callToActionUrl,
            'channelId' => $this->channelId,
            'content' => $this->content,
            'dateStart' => $this->dateStart,
            'emoji' => $this->emoji,
            'heading' => $this->heading,
            'id' => $this->id,
            'markup' => $this->markup,
            'metrics' => $this->metrics,
            'mjml' => $this->mjml,
            'pixel' => $this->pixel,
            'positioning' => $this->positioning,
            'promoterDisplayName' => $this->promoterDisplayName,
            'promoterImage' => $this->promoterImage,
            'promoterImageAlternativeText' => $this->promoterImageAlternativeText,
            'resolvedCallToActionUrl' => $this->resolvedCallToActionUrl,
            'uniqueId' => $this->uniqueId,
            'scheduledDate' => $this->scheduledDate,
            'status' => $this->status,
        ];
    }

    public function getAdTypeId(): int
    {
        return $this->adTypeId;
    }

    public function getBlurb(): string
    {
        return $this->blurb;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getClicks(): int
    {
        $metrics = $this->getMetrics();
        return isset($metrics->clicks) ? $metrics->clicks : 0;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDateStart(): string
    {
        return $this->dateStart;
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function getHeading(): string
    {
        return $this->heading;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMarkup(): string
    {
        return $this->markup;
    }

    public function getMjml(): string
    {
        return $this->mjml;
    }

    public function getPositioning(): int
    {
        return $this->positioning;
    }

    public function getPromoterDisplayName(): string
    {
        return $this->promoterDisplayName;
    }

    public function getPromoterImage(): string
    {
        return $this->promoterImage;
    }

    private function getMetrics(): ?\stdClass
    {
        return $this->metrics;
    }

    public function getResolvedCallToActionUrl(): string
    {
        return $this->resolvedCallToActionUrl;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getViews(): int
    {
        $metrics = $this->getMetrics();

        return isset($metrics->reads) ? $metrics->reads : 0;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
