<?php

namespace App\DTOs;

use App\Collections\LetterPartCollection;
use App\Collections\LettersEmailsCollection;
use App\Collections\LettersUsersCollection;
use App\Models\Letter;

/**
 * @package App\DTOs
 */
class LetterDto
{
    public $authors;
    public $campaignId;
    public $channelId;
    public $copyRendered;
    public $createdAt;
    public $deletedAt;
    public $emails;
    public $emailServiceProvider;
    public $emailServiceProviderListId;
    public $emailTemplate;
    public $id;
    public $includePromotions;

    /**
     * The MJML render of the Letter.
     * @var string
     */
    public $mjmlTemplate;
    public $parts;
    public $promotions;
    public $publicationDate;
    public $publicationDateOffset;
    public $publicationStatus;
    public $publishIntent;
    public $segmentId;
    public $slug;
    public $subtitle;
    public $specialBanner;
    public $title;
    public $updatedAt;

    /**
     * @var string The unique identifier of the letter.
     */
    public $uniqueId;

    public function __construct(
        \stdClass $object = null,
        Letter $letter = null,
        LetterPartCollection $partsCollection = null,
        LettersEmailsCollection $emailsCollection = null,
        LettersUsersCollection $usersCollection = null
    ) {
        if (!empty($object)) {
            $this->channelId = $object->channelId;
            $this->campaignId = $object->campaignId;
            $this->copyRendered = $object->copyRendered;
            $this->createdAt = $object->created_at;
            $this->deletedAt = $object->deleted_at;
            $this->emailServiceProvider = $object->emailServiceProvider;
            $this->emailServiceProviderListId = $object->emailServiceProviderListId;
            $this->emailTemplate = $object->emailTemplate;
            $this->id = $object->id;
            $this->includePromotions = (bool) $object->includePromotions;

            $this->mjmlTemplate = $object->mjmlTemplate;

            $publicationDate = $object->publicationDate;
            $this->publicationDate = empty($publicationDate) ? '' : $publicationDate;

            $this->publicationDateOffset = $object->publicationDateOffset;
            $this->publicationStatus = $object->status;
            $this->segmentId = $object->segmentId;
            $this->slug = $object->slug;
            $this->subtitle = $object->subtitle;
            $this->specialBanner = $object->specialBanner;
            $this->title = $object->title;
            $this->updatedAt = $object->updated_at;
            $this->uniqueId = isset($object->uniqueId) ? $object->uniqueId : '';
        }

        if (!empty($letter)) {
            $this->channelId = $letter->getChannelId();
            $this->campaignId = $letter->getCampaignId();
            $this->copyRendered = $letter->getCopyRendered();
            $this->createdAt = $letter->getCreatedAt();
            $this->deletedAt = $letter->getDeletedAt();
            $this->emailServiceProvider = $letter->getEmailServiceProvider();
            $this->emailServiceProviderListId = $letter->getEmailServiceProviderListId();
            $this->emailTemplate = $letter->getEmailTemplate();
            $this->id = $letter->getId();
            $this->includePromotions = $letter->getIncludePromotions();
            $this->mjmlTemplate = $letter->getMjmlTemplate();

            $publicationDate = $letter->getPublicationDate();
            $this->publicationDate = $publicationDate === '' ? null : $publicationDate;

            $this->publicationDateOffset = $letter->getPublicationDateOffset();
            $this->publicationStatus = $letter->getPublicationStatus();
            $this->publishIntent = $letter->getPublishIntent();
            $this->segmentId = $letter->getSegmentId();
            $this->slug = $letter->getSlug();
            $this->subtitle = $letter->getSubtitle();
            $this->specialBanner = $letter->getSpecialBanner();
            $this->title = $letter->getTitle();
            $this->updatedAt = $letter->getUpdatedAt();
            $this->uniqueId = $letter->getUniqueId();
        }

        $this->authors = empty($usersCollection) ? new LettersUsersCollection() : $usersCollection;
        $this->emails = empty($emailsCollection) ? new LettersEmailsCollection() : $emailsCollection;
        $this->parts = empty($partsCollection) ? new LetterPartCollection() : $partsCollection;
    }

    public function mapChannelDtoToDatabaseColumns(): array
    {
        return [
            'id' => $this->id,
            'includePromotions' => $this->includePromotions,
            'campaignId' => $this->campaignId,
            'channelId' => $this->channelId,
            'copyRendered' => $this->copyRendered,
            'emailServiceProvider' => $this->emailServiceProvider,
            'emailServiceProviderListId' => $this->emailServiceProviderListId,
            'emailTemplate' => $this->emailTemplate,
            'mjmlTemplate' => $this->mjmlTemplate,
            'publicationDate' => $this->publicationDate,
            'publicationDateOffset' => $this->publicationDateOffset,
            'status' => $this->publicationStatus,
            'publishIntent' => $this->publishIntent,
            'segmentId' => $this->segmentId,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'specialBanner' => $this->specialBanner,
            'title' => $this->title,
            'deleted_at' => $this->deletedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'uniqueId' => $this->uniqueId,
        ];
    }
}
