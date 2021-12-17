<?php

namespace App\DTOs;

/**
 * Our `ChannelConfiguration` model holds the value of a given configuration setting and
 * associates it with a specific brand;
 *
 * Class ChannelConfigurationDto
 * @package App\DTOs
 * @see https://airtable.com/tbltoyzycjaOSbWoK/viwkJrDvYNIJHsZ9O/reccZLPXf3kBT5Npv?blocks=hide
 */
class ChannelConfigurationDto
{
    public $channelConfigurationValue;
    public $channelId;
    public $configurationId;
    public $configurationName;
    public $configurationSlug;
    public $createdAt;
    public $dataType;
    public $id;
    public $updatedAt;


    public function __construct(\stdClass $object = null)
    {
        $this->dataType = isset($object->dataType) ? $object->dataType : 'string';
        $this->channelConfigurationValue = isset($object->channelConfigurationValue) ? $this->transformConfigurationChannelValue($object->channelConfigurationValue) : '';
        $this->channelId = isset($object->channelId) ? $object->channelId : null;
        $this->configurationId = isset($object->configurationId) ? $object->configurationId : null;
        $this->configurationName = isset($object->configurationName) ? $object->configurationName : '';
        $this->configurationSlug = isset($object->configurationSlug) ? $object->configurationSlug : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    private function transformConfigurationChannelValue($value)
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
            case 'boolean':
                return (bool) $value;
            case 'object':
                return (object) json_decode($value);
        }
    }
}
