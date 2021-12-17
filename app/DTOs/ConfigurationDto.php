<?php

namespace App\DTOs;

/**
 * Our `Configuration` model, is a simple way for us to provide configuration options
 * globally across the platform, where users can store their own configuration values.
 *
 * Class ConfigurationDto
 * @package App\DTOs
 * @see https://airtable.com/tbltoyzycjaOSbWoK/viwkJrDvYNIJHsZ9O/recKa4X1wfeKxs3Aj?blocks=hide
 */
class ConfigurationDto
{
    public $configurationName;
    public $configurationSlug;
    public $dataType;
    public $id;

    public function __construct(\stdClass $object = null)
    {
        $this->configurationName = isset($object->configurationName) ? $object->configurationName : '';
        $this->configurationSlug = isset($object->configurationSlug) ? $object->configurationSlug : '';
        $this->dataType = isset($object->dataType) ? $object->dataType : 'string';
        $this->id = isset($object->id) ? $object->id : null;
    }
}
