<?php

namespace App\DTOs;

/**
 * @package App\DTOs
 */
class EmailDto
{
    public $brandId;
    public $channelId;
    public $content;
    public $createdAt;
    public $description;
    public $deletedAt;
    public $fromEmail;
    public $fromName;
    public $id;
    public $name;
    public $subject;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->brandId = isset($object->brandId) ? $object->brandId : null;
        $this->channelId = isset($object->channelId) ? $object->channelId : null;
        $this->content = isset($object->content) ? $object->content : '';
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->description = isset($object->description) ? $object->description : '';
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : '';
        $this->fromEmail = isset($object->fromEmail) ? $object->fromEmail : '';
        $this->fromName = isset($object->fromName) ? $object->fromName : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->name = isset($object->name) ? $object->name : '';
        $this->subject = isset($object->subject) ? $object->subject : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    public function mapEmailDtoToDatabaseColumns(): array
    {
        return [
          'brandId' => $this->brandId,
          'channelId' => $this->channelId,
          'content' => $this->content,
          'created_at' => $this->createdAt,
          'description' => $this->description,
          'deleted_at' => $this->deletedAt,
          'fromEmail' => $this->fromEmail,
          'fromName' => $this->fromName,
          'id' => $this->id,
          'name' => $this->name,
          'subject' => $this->subject,
          'updated_at' => $this->updatedAt,
        ];
    }
}
