<?php

namespace App\Http\Repositories;

use App\DTOs\BrandDto;
use App\Collections\BrandCollection;

interface BrandRepositoryInterface
{
    public function createBrandFeaturesAndConfigurations(BrandDto $dto): ?BrandDto;
    public function deleteBrand(BrandDto $brandDto): bool;
    public function getBrandById(int $brandId): ?BrandDto;
    public function getBrandBySlug(string $brandSlug): ?BrandDto;

    /**
     * @return BrandDto[]
     */
    public function getBrands(): BrandCollection;

    /**
     * @return BrandCollection
     */
    public function getBrandsByIds(array $brandIds): BrandCollection;
    public function updateBrand(BrandDto $dto): ?BrandDto;
    public function getBrandKeyByBrandId(int $brandId): ?string;
    public function updateBrandConfiguration(
        int $configurationId,
        $brandConfigurationValue,
        int $brandId
    ): bool;
    public function updateBrandConfigurationByBrandCofigurationValue($brandConfigurationValue, string $brandConfiguration): bool;
}
