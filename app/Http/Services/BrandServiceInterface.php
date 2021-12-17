<?php

namespace App\Http\Services;

use App\Collections\BrandCollection;
use App\Models\Brand;
use App\Models\Configuration;

interface BrandServiceInterface
{
    public function createBrand(Brand $brand): ?Brand;
    public function deleteBrand(Brand $brand): bool;
    public function getBrandApiKeyByBrandId(int $brandId): ?string;
    public function getBrandById(int $brandId): ?Brand;
    public function getBrandBySlug(string $slug): ?Brand;
    public function getBrands(): BrandCollection;
    public function getBrandsByIds(array $arrayOfBrandIds): BrandCollection;
    public function getConfigurationBySlug(string $configurationSlug): ?Configuration;
    public function setBrandConfiguration($brandConfigurationValue, int $brandId, int $configurationid): bool;
    public function updateBrand(Brand $brand): ?Brand;
    public function updateChannelConfiguration(int $channelId, $channelConfigurationValue, int $configurationId): bool;
    public function updateBrandConfigurationWithStripeDto(string $code, int $brandId): bool;
    public function updateBrandConfigurationByBrandConfigurationValue($brandConfigurationsValue, string $brandConfiguration): bool;
}
