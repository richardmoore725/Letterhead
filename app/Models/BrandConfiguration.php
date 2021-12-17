<?php

namespace App\Models;

use App\DTOs\BrandConfigurationDto;

class BrandConfiguration
{
    private $brandConfigurationValue;
    private $brandId;
    private $configurationId;
    private $configurationName;
    private $configurationSlug;
    private $dataType;
    private $id;

    public function __construct(BrandConfigurationDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->brandConfigurationValue = $dto->brandConfigurationValue;
        $this->brandId = $dto->brandId;
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

    public function getBrandConfigurationValue()
    {
        return $this->brandConfigurationValue;
    }

    public function getConfigurationSlug(): string
    {
        return $this->configurationSlug;
    }
}
