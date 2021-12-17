<?php

namespace App\Models;

use App\Collections\LetterPartCollection;
use App\Collections\LettersEmailsCollection;
use App\Collections\LettersUsersCollection;
use App\DTOs\LetterDto;

class Letter
{
    public const LETTER_ESP_MAILCHIMP = 1;
    public const PUBLICATION_STATUS_DRAFT = 0;
    public const PUBLICATION_STATUS_PUBLISHED = 1;
    public const PUBLICATION_STATUS_SCHEDULED = 2;
    public const PUBLICATION_STATUS_TEMPLATE = 3;
    public const PUBLICATION_STATUS_REVIEW = 4;
    public const PUBLISH_INTENT_EMAIL = 'email';


    private static $allowedPublishIntents = [
      self::PUBLISH_INTENT_EMAIL,
    ];

    /**
     * An array of users we credit as authors.
     * @var LettersUsersCollection
     */
    private $authors;

    /**
     * It will only be set in sendEmailThroughMailChimpJob
     * It is an '' when in a letter without being sent
     */
    private $campaignId;

    /**
     * The ID of the channel this Letter belongs to.
     * @var integer
     */
    private $channelId;

    /**
     * All of the copy of a letter, including with promotions inserted, rendered
     * together. This is ultimately what we email.
     *
     * @var String
     */
    private $copyRendered;

    private $createdAt;
    private $deletedAt;

    /**
     * An array of Emails through which this newsletter
     * has been sent.
     *
     * @var LettersEmailsCollection
     */
    private $emails;

    /**
     * The EmailServiceProvider is an integer indicator of which ESP -- what we call services like
     * MailChimp or ConstantContact -- a newsletter should be emailed through.
     *
     * By default, the EmailServiceProvider for a specific newsletter doesn't yet exist or
     * hasn't been set, so we will default it to zero.
     *
     * @var int $emailServiceProvider
     */
    private $emailServiceProvider = 0;

    /**
     * Most EmailServiceProviders provide different _lists_ that have string ListIds. They define
     * specific groups of subscribers as part of a larger EmailServiceProvider account. For instance,
     * on MailChimp, you may have _many_ newsletters but a single account. Generally, Lists align
     * with Newsletters.
     *
     * By default, the EmailServiceProviderListId doesn't yet exist, so we'll set it as an
     * empty string.
     *
     * @var string $emailServiceProviderListId
     */
    private $emailServiceProviderListId = '';

    /**
     * The content of the letter rendered as HTML.
     * @var string
     */
    private $emailTemplate = '';

    /**
     * The ID of this Letter
     * @var integer
     */
    private $id;

    /**
     * The boolean of whether this letter includes promotions
     */
    private $includePromotions;

    /**
     * The content of the Letter rendered as MJML.
     * @var string
     */
    private $mjmlTemplate;

    /**
     * An array of LetterParts that compose the core
     * content of this letter.
     * @var LetterPartCollection
     */
    private $parts;

    /**
     * The date of publication
     * @var \DateTime
     */
    private $publicationDate;

    /**
     * The timezone offset the letter will be sent in.
     * @var String
     */
    private $publicationDateOffset;

    /**
     * The status of this publication, whether it is
     * scheduled, a draft, or whatever.
     * @var integer
     */
    private $publicationStatus;

    /**
     * The intended action when the newsletter is published.  Note that we default to email
     * @var string
     */
    private $publishIntent = self::PUBLISH_INTENT_EMAIL;

    /**
     * The id of a mailchimplist segment
     * @var int
     */
    private $segmentId;

    /**
     * The URL slug
     * @var string
     */
    private $slug;

    /**
     * The Title of this letter (also doubles as the
     * email subject).
     * @var string
     */
    private $subtitle;

    /**
     * This will overwrite channel default horizontal logo
     * when it is not empty.
     * @var string
     */
    private $specialBanner;

    /**
     * The subtitle (also doubles as the email preview).
     * @var string
     */
    private $title;

    private $updatedAt;

    /**
     * The unique identifier of the letter.
     * @var string
     */
    private $uniqueId;

    public function __construct(LetterDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->authors = $dto->authors;
        $this->campaignId = $dto->campaignId;
        $this->channelId = $dto->channelId;
        $this->copyRendered = $dto->copyRendered;
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->emails = $dto->emails;
        $this->emailServiceProvider = $dto->emailServiceProvider;
        $this->emailServiceProviderListId = $dto->emailServiceProviderListId;
        $this->emailTemplate = $dto->emailTemplate;
        $this->id = $dto->id;
        $this->includePromotions = $dto->includePromotions;
        $this->mjmlTemplate = $dto->mjmlTemplate;
        $this->parts = $dto->parts;
        $this->publicationDate = $dto->publicationDate;
        $this->publicationDateOffset = $dto->publicationDateOffset;
        $this->publicationStatus = $dto->publicationStatus;
        $this->segmentId = $dto->segmentId;
        $this->slug = $dto->slug;
        $this->subtitle = $dto->subtitle;
        $this->specialBanner = $dto->specialBanner;
        $this->title = $dto->title;
        $this->updatedAt = $dto->updatedAt;
        $this->uniqueId = $dto->uniqueId;
    }

    public function convertToArray(): array
    {
        return [
            'authors' => $this->getAuthors()->getPublicArray(),
            'campaignId' => $this->getCampaignId(),
            'channelId' => $this->getChannelId(),
            'createdAt' => $this->getCreatedAt(),
            'copyRendered' => $this->getCopyRendered(),
            'deletedAt' => $this->getDeletedAt(),
            'emails' => $this->getEmails()->getPublicArray(),
            'id' => $this->getId(),
            'includePromotions' => $this->getIncludePromotions(),
            'parts' => $this->getParts()->getPublicArray(),
            'publicationDate' => $this->getPublicationDate(),
            'publicationDateOffset' => $this->getPublicationDateOffset(),
            'publicationStatus' => $this->getPublicationStatus(),
            'publishIntent' => $this->getPublishIntent(),
            'segmentId' => $this->getSegmentId(),
            'slug' => $this->getSlug(),
            'subtitle' => $this->getSubtitle(),
            'specialBanner' => $this->getSpecialBanner(),
            'title' => $this->getTitle(),
            'updatedAt' => $this->getUpdatedAt(),
            'uniqueId' => $this->getUniqueId(),
        ];
    }

    public function convertToDto(): LetterDto
    {
        return new LetterDto(null, $this);
    }

    public function getAuthors(): LettersUsersCollection
    {
        return $this->authors;
    }

    public function getCampaignId(): string
    {
        return $this->campaignId;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getCopyRendered(): string
    {
        return $this->copyRendered ?? '';
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function getEmails(): LettersEmailsCollection
    {
        return $this->emails;
    }

    public function getEmailServiceProvider(): int
    {
        return $this->emailServiceProvider;
    }

    public function getEmailServiceProviderListId(): string
    {
        return $this->emailServiceProviderListId;
    }

    public function getEmailTemplate(): string
    {
        return $this->emailTemplate ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIncludePromotions(): bool
    {
        return empty($this->includePromotions) ? false : $this->includePromotions;
    }

    /**
     * The MJML template of the Letter. If one's not been generated, we'll return
     * an empty string.
     *
     * @return string
     */
    public function getMjmlTemplate(): string
    {
        return $this->mjmlTemplate ?? '';
    }

    public function getParts(): LetterPartCollection
    {
        return $this->parts;
    }

    public function getPublicationDate(): string
    {
        return $this->publicationDate;
    }

    public function getPublicationDateOffset(): string
    {
        return $this->publicationDateOffset;
    }

    public function getPublicationStatus(): int
    {
        return $this->publicationStatus;
    }

    public function getPublishIntent(): string
    {
        return $this->publishIntent;
    }

    public function getSegmentId(): int
    {
        return $this->segmentId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getSpecialBanner(): string
    {
        return $this->specialBanner;
    }

    /**
     * Construct a tracking pixel using the Letter's unique identifier and a
     * `SERVICE_PIXEL_URL` that is defined at runtime.
     *
     * @todo This pixel assumes a MailChimp email service provider, and will need to
     * be modified later with future ESP support.
     *
     * @return string
     */
    public function getTrackingPixel(): string
    {
        $pixelServiceUrl = env('SERVICE_PIXEL_URL', '');
        $uniqueIdentifier = $this->getUniqueId();
        $pixelUrl = "{$pixelServiceUrl}/{$uniqueIdentifier}/*|UNIQID|*/l.jpg";

        return $pixelUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId ?? '';
    }

    /**
     * Lumen-side validation rules we use to ensure that POSTs include the
     * required values.
     *
     * @return array
     * @see https://laravel.com/docs/7.x/validation#available-validation-rules
     */
    public static function getValidationRules(): array
    {
        return [
            'authors' => 'required|array',
            'authors.*' => 'required|integer',
            'campaignId' => 'nullable|string',
            'channelId' => 'required|integer',
            'copyRendered' => 'nullable|string',
            'delta' => 'nullable|string',
            'emailTemplate' => 'nullable|string',
            'id' => 'nullable|integer',
            'includePromotions' => 'required',
            'letterParts' => 'nullable|array',
            'letterParts.*.copy' => 'nullable|string',
            'letterParts.*.id' => 'nullable|integer',
            'letterParts.*.heading' => 'nullable|string',
            'mjmlTemplate' => 'nullable|string',
            'publicationDate' => 'nullable|string',
            'publicationDateOffset' => 'nullable|string',
            'publicationStatus' => 'required|integer',
            'segmentId' => 'required|int',
            'slug' => 'nullable|string',
            'subtitle' => 'required|string',
            'specialBanner' => 'nullable|string',
            'title' => 'required|string',
        ];
    }

    public function setCampaignId(string $campaignId): void
    {
        $this->campaignId = $campaignId;
    }

    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function setCopyRendered(string $copyRendered): void
    {
        $this->copyRendered = $copyRendered;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setEmailServiceProvider(int $emailServiceProvider): void
    {
        $this->emailServiceProvider = $emailServiceProvider;
    }

    public function setEmailServiceProviderListId(string $emailServiceProviderListId): void
    {
        $this->emailServiceProviderListId = $emailServiceProviderListId;
    }

    public function setEmailTemplate(string $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * Set the MJML template of the Letter.
     * @param string $mjmlTemplate
     */
    public function setMjmlTemplate(string $mjmlTemplate): void
    {
        $this->mjmlTemplate = $mjmlTemplate;
    }

    public function setPublicationDate(string $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    public function setPublicationDateOffset(string $publicationDateOffset): void
    {
        $this->publicationDateOffset = $publicationDateOffset;
    }

    public function setPublicationStatus(int $publicationStatus): void
    {
        $this->publicationStatus = $publicationStatus;
    }

    public function setPublishIntent(string $publishIntent): void
    {
        self::isValidPublishIntentOrThrow($publishIntent);

        $this->publishIntent = strtolower($publishIntent);
    }

    public function setIncludePromotions(bool $includePromotions): void
    {
        $this->includePromotions = (bool) $includePromotions;
    }

    public function setSegmentId(int $segmentId): void
    {
        $this->segmentId = $segmentId;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function setSpecialBanner(string $specialBanner): void
    {
        $this->specialBanner = $specialBanner;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public static function isValidPublishIntent(string $inputPublishIntent): bool
    {
        $publishIntent = strtolower($inputPublishIntent);
        return in_array($publishIntent, self::$allowedPublishIntents);
    }

    public static function isValidPublishIntentOrThrow(string $inputPublishIntent): bool
    {
        if (!self::isValidPublishIntent($inputPublishIntent)) {
            throw new \Exception("The given Publish Intent '$inputPublishIntent'");
        }
    }
}
