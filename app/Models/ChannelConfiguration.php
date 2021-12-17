<?php

namespace App\Models;

use App\DTOs\ChannelConfigurationDto;

class ChannelConfiguration
{
    private $channelConfigurationValue;
    private $channelId;
    private $configurationId;
    private $configurationName;
    private $configurationSlug;
    private $dataType;
    private $id;

    public function __construct(ChannelConfigurationDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->channelConfigurationValue = $dto->channelConfigurationValue;
        $this->channelId = $dto->channelId;
        $this->configurationId = $dto->configurationId;
        $this->configurationName = $dto->configurationName;
        $this->configurationSlug = $dto->configurationSlug;
        $this->dataType = $dto->dataType;
        $this->id = $dto->id;
    }

    public function convertToArray(): array
    {
        return get_object_vars($this);
    }

    public function getChannelConfigurationValue()
    {
        return $this->channelConfigurationValue;
    }

    public function getConfigurationId(): int
    {
        return $this->configurationId;
    }

    public function getConfigurationSlug(): string
    {
        return $this->configurationSlug;
    }
}
