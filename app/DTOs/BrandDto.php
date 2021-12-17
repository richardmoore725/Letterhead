<?php

namespace App\DTOs;

/**
 * Our `Brand` model describes - well - one of our brands like The New Tropic or
 * Bridgeliner. This tends to refer to the organization behind the product, but
 * right now names like "The Incline" refer to both the people who make The Incline
 * and its newsletter. Functionally, we distinguish these. Brands are made-up of
 * people who share a banner - like House Stark.
 *
 * Class BrandDto
 * @package App\DTOs
 * @see https://airtable.com/tbltoyzycjaOSbWoK/viwkJrDvYNIJHsZ9O/recx7MlY7bApf6aI5?blocks=hide
 */
class BrandDto
{
    public $brandConfigurations;
    public $brandHorizontalLogo;
    public $brandName;
    public $brandSlug;
    public $brandSquareLogo;
    public $channels;
    public $createdAt;
    public $id;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->brandConfigurations = isset($object->brandConfigurations) ? $object->brandConfigurations : null;
        $this->brandHorizontalLogo = isset($object->brandHorizontalLogo) ? $object->brandHorizontalLogo : '';
        $this->brandName = isset($object->brandName) ? $object->brandName : '';
        $this->brandSlug = isset($object->brandSlug) ? $object->brandSlug : '';
        $this->brandSquareLogo = isset($object->brandSquareLogo) ? $object->brandSquareLogo : '';
        $this->channels = isset($object->channels) ? $object->channels : [];
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    /**
     * When we insert data using Lumen's methods, we have to pass each column
     * and its value as an array. We use this method to map the relevant
     * properties from the Dto to the column.
     *
     * @return array
     */
    public function mapToDatabaseColumns(): array
    {
        return [
            'brandHorizontalLogo' => $this->brandHorizontalLogo,
            'brandName' => $this->brandName,
            'brandSlug' => $this->brandSlug,
            'brandSquareLogo' => $this->brandSquareLogo,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'updated_at' => $this->updatedAt,
        ];
    }
}
