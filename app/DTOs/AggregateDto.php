<?php

namespace App\DTOs;

use App\Models\Aggregate;

class AggregateDto
{
    public $archived;
    public $channelId;
    public $createdAt;
    public $curated;
    public $dateOfAggregatePublication;
    public $deletedAt;
    public $excerpt;
    public $id;
    public $image;
    public $letterId;
    public $originalUrl;
    public $siteName;
    public $title;
    public $uniqueId;
    public $updatedAt;


    public function __construct(\stdClass $object = null, Aggregate $aggregate = null)
    {
        if (!empty($object)) {
            $this->archived = $object->archived;
            $this->channelId = $object->channelId;
            $this->createdAt = $object->created_at;
            $this->curated = (bool) $object->curated;
            $this->dateOfAggregatePublication = $object->dateOfAggregatePublication;
            $this->deletedAt = $object->deleted_at;
            $this->excerpt = $object->excerpt;
            $this->id = $object->id;
            $this->image = $object->image;
            $this->letterId = $object->letterId;
            $this->originalUrl = $object->originalUrl;
            $this->siteName = $object->siteName;
            $this->title = $object->title;
            $this->uniqueId = $object->uniqueId;
            $this->updatedAt = $object->updated_at;
        }

        if (!empty($aggregate)) {
            $this->archived = $aggregate->getArchived();
            $this->channelId = $aggregate->getChannelId();
            $this->createdAt = $aggregate->getCreatedAt();
            $this->curated = (bool) $aggregate->getCurated();
            $this->dateOfAggregatePublication = $aggregate->getDateOfAggregatePublication();
            $this->deletedAt = $aggregate->getDeletedAt();
            $this->excerpt = $aggregate->getExcerpt();
            $this->id = $aggregate->getId();
            $this->image = $aggregate->getImage();
            $this->letterId = $aggregate->getLetterId();
            $this->originalUrl = $aggregate->getOriginalUrl();
            $this->siteName = $aggregate->getSiteName();
            $this->title = $aggregate->getTitle();
            $this->uniqueId = $aggregate->getUniqueId();
            $this->updatedAt = $aggregate->getUpdatedAt();
        }
    }

    public function mapToDatabaseColumns(): array
    {
        return [
            'archived' => (int) $this->archived,
            'channelId' => $this->channelId,
            'created_at' => $this->createdAt,
            'curated' => (int) $this->curated,
            'dateOfAggregatePublication' => $this->dateOfAggregatePublication,
            'deleted_at' => $this->deletedAt,
            'excerpt' => $this->excerpt,
            'id' => $this->id,
            'image' => $this->image,
            'letterId' => $this->letterId,
            'originalUrl' => $this->originalUrl,
            'title' => $this->title,
            'siteName' => $this->siteName,
            'uniqueId' => $this->uniqueId,
            'updated_at' => $this->updatedAt,
        ];
    }
}
