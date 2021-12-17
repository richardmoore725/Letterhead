<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;

/**
 * We use `BrandKeyDto` to transform the properties and values associated with
 * Brand Keys to and from the database. We use DTOs to ensure our data is
 * always in the form we need in our app, which helps reduce type and other
 * checks.
 *
 * Class BrandKeyDto
 * @package App\DTOs
 */
class BrandKeyDto
{
    public $brandId;
    public $createdAt;
    public $deletedAt;
    public $id;
    public $isActive;
    public $key;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->brandId = isset($object->brandId) ? $object->brandId : null;
        $this->createdAt = isset($object->created_at) ? $object->created_at : CarbonImmutable::now()->toDateTimeString();
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : null;
        $this->id = isset($object->id) ? $object->id : null;
        $this->isActive = isset($object->isActive) ? $object->isActive : true;
        $this->key = isset($object->key) ? $object->key : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : CarbonImmutable::now()->toDateTimeString();
    }

    /**
     * An array that maps the values of our Dto into an
     * insertable-friendly form, as per the Lumen query
     * builder.
     *
     * @return array
     */
    public function convertToArrayOfDatabaseColumns(): array
    {
        return [
            'brandId' => $this->brandId,
            'created_at' => $this->createdAt,
            'deleted_at' => $this->deletedAt,
            'id' => $this->id,
            'isActive' => $this->isActive,
            'key' => $this->key,
            'updated_at' => $this->updatedAt
        ];
    }
}
