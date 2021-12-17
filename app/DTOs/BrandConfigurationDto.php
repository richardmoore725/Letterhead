<?php

namespace App\DTOs;

/**
 * Our `BrandConfiguration` model holds the value of a given configuration setting and
 * associates it with a specific brand;
 *
 * Class BrandConfigurationDto
 * @package App\DTOs
 * @see https://airtable.com/tbltoyzycjaOSbWoK/viwkJrDvYNIJHsZ9O?blocks=hide
 */
class BrandConfigurationDto
{
    public $brandConfigurationValue;
    public $brandId;
    public $configurationId;
    public $configurationName;
    public $configurationSlug;
    public $dataType;

    public $id;

    public function __construct(\stdClass $object = null)
    {
        $this->dataType = isset($object->dataType) ? $object->dataType : 'string';
        $this->brandConfigurationValue = isset($object->brandConfigurationValue) ? $this->transformConfigurationValue($object->brandConfigurationValue) : '';
        $this->brandId = isset($object->brandId) ? $object->brandId : null;
        $this->configurationId = isset($object->configurationId) ? $object->configurationId : null;
        $this->configurationName = isset($object->configurationName) ? $object->configurationName : '';
        $this->configurationSlug = isset($object->configurationSlug) ? $object->configurationSlug : '';
        $this->id = isset($object->id) ? $object->id : null;
    }

    private function transformConfigurationValue($value)
    {
        switch ($this->dataType) {
            case 'string':
                return $value;
            case 'array':
                if (is_array($value)) {
                    return $value;
                }

                if (is_string($value)) {
                    try {
                        return unserialize($value);
                    } catch (\Exception $e) {
                        return explode(',', $value);
                    }
                }

                return is_array($value) ? serialize($value) : $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
        }
    }
}
