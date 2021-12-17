<?php

namespace App\Models;

use App\DTOs\ConfigurationDto;

class Configuration
{
    private $configurationName;
    private $configurationSlug;
    private $dataType;
    private $id;

    public function __construct(ConfigurationDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->configurationName = $dto->configurationName;
        $this->configurationSlug = $dto->configurationSlug;
        $this->dataType = $dto->dataType;
        $this->id = $dto->id;
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConfigurationSlug(): string
    {
        return $this->configurationSlug;
    }
}
