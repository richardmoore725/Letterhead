<?php

namespace App\Collections;

use App\DTOs\BrandConfigurationDto;
use App\Models\BrandConfiguration;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class BrandConfigurationCollection extends BaseCollection
{
    public function __construct(Collection $brandConfigurationDatabaseResults)
    {
        $dtos = $this->convertToDtoArray($brandConfigurationDatabaseResults);
        $brandConfigurations = $this->toModelArray($dtos);
        parent::__construct($brandConfigurations);
    }

    public function convertToDtoArray(Collection $brandConfigurationDatabaseResults): array
    {
        return array_map(function ($brandConfiguration) {
            return new BrandConfigurationDto($brandConfiguration);
        }, $brandConfigurationDatabaseResults->toArray());
    }

    public function getAdvertisingRevenueShare(): float
    {
        return $this->getConfigurationValueBySlug('advertisingRevenueShare', 0.05);
    }

    public function getBrandContactAddress__city(): string
    {
        return $this->getConfigurationValueBySlug('brandContactAddress__city', '');
    }

    public function getBrandContactAddress__postal(): string
    {
        return $this->getConfigurationValueBySlug('brandContactAddress__postal', '');
    }

    public function getBrandContactAddress__state(): string
    {
        return $this->getConfigurationValueBySlug('brandContactAddress__state', '');
    }

    public function getBrandContactAddress__street(): string
    {
        return $this->getConfigurationValueBySlug('brandContactAddress__street', '');
    }

    public function getBrandContactEmail(): string
    {
        return $this->getConfigurationValueBySlug('brandContactEmail', '');
    }

    public function getBrandContactName(): string
    {
        return $this->getConfigurationValueBySlug('brandContactName', '');
    }

    public function getBrandContactPhone(): string
    {
        return $this->getConfigurationValueBySlug('brandContactPhone', '');
    }
    public function getBrandUrl(): string
    {
        return $this->getConfigurationValueBySlug('brandUrl', '');
    }

    /**
     * Given the `slug` of a configuration, we'll check to see if there is a value set and
     * retrieve either that or return a default.
     *
     * @param string $slug
     * @param $defaultValue
     * @return mixed
     */
    private function getConfigurationValueBySlug(string $slug, $defaultValue)
    {
        /**
         * @var BrandConfiguration|null
         */
        $configuration = $this->first(function (BrandConfiguration $brandConfiguration) use ($slug) {
            return $brandConfiguration->getConfigurationSlug() === $slug;
        });

        return empty($configuration) ? $defaultValue : $configuration->getBrandConfigurationValue();
    }

    public function getPublicArray(): array
    {
        return array_map(function (BrandConfiguration $brandConfiguration) {
            return $brandConfiguration->convertToArray();
        }, $this->items);
    }

    public function getStripeAccount(): string
    {
        return $this->getConfigurationValueBySlug('stripeAccount', '');
    }

    public function toModelArray(array $dtos): array
    {
        return array_map(function (BrandConfigurationDto $dto) {
            return new BrandConfiguration($dto);
        }, $dtos);
    }
}
