<?php

namespace App\Http\Services;

use App\Collections\BrandCollection;
use App\Http\Repositories\BrandRepositoryInterface;
use App\Http\Repositories\ChannelRepositoryInterface;
use App\Http\Repositories\ConfigurationRepositoryInterface;
use App\Http\Repositories\StripeRepositoryInterface;
use App\Models\Brand;
use App\Models\Configuration;

class BrandService implements BrandServiceInterface
{
    private $brandRepository;
    private $channelRepository;
    private $configurationRepository;
    private $stripeRepository;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        ChannelRepositoryInterface $channelRepository,
        ConfigurationRepositoryInterface $configurationRepository,
        StripeRepositoryInterface $stripeRepository
    ) {
        $this->brandRepository = $brandRepository;
        $this->channelRepository = $channelRepository;
        $this->configurationRepository = $configurationRepository;
        $this->stripeRepository = $stripeRepository;
    }

    public function createBrand(Brand $brand): ?Brand
    {
        $brandDto = $this->brandRepository->createBrandFeaturesAndConfigurations($brand->convertToDto());
        if (empty($brandDto)) {
            return null;
        }

        return new Brand($brandDto);
    }

    /**
     * Deleting a brand will also trash all corresponding BrandFeatures, Brand Configurations,
     * Channels, and their ChannelConfigurations.
     *
     * @param Brand $brand
     * @return bool
     */
    public function deleteBrand(Brand $brand): bool
    {
        return $this->brandRepository->deleteBrand($brand->convertToDto());
    }

    public function getBrandById(int $brandId): ?Brand
    {
        $dto = $this->brandRepository->getBrandById($brandId);
        if (empty($dto)) {
            return null;
        }

        $dto->channels = $this->channelRepository->getChannelsByBrandId($brandId);

        return new Brand($dto);
    }

    public function getBrandBySlug(string $slug): ?Brand
    {
        $dto = $this->brandRepository->getBrandBySlug($slug);

        if (empty($dto)) {
            return null;
        }

        $dto->channels = $this->channelRepository->getChannelsByBrandId($dto->id);

        return new Brand($dto);
    }

    public function getBrands(): BrandCollection
    {
        return $this->brandRepository->getBrands();
    }

    public function getBrandsByIds(array $arrayOfBrandIds): BrandCollection
    {
        return $this->brandRepository->getBrandsByIds($arrayOfBrandIds);
    }

    public function getConfigurationBySlug(string $configurationSlug): ?Configuration
    {
        $dto = $this->configurationRepository->getConfigurationBySlug($configurationSlug);

        return empty($dto) ? null : new Configuration($dto);
    }

    public function updateBrand(Brand $brand): ?Brand
    {
        $updatedDto = $this->brandRepository->updateBrand($brand->convertToDto());
        return empty($updatedDto) ? null : new Brand($updatedDto);
    }

    public function updateChannelConfiguration(int $channelId, $channelConfigurationValue, int $configurationId): bool
    {
        return $this->channelRepository->updateChannelConfiguration($channelId, $channelConfigurationValue, $configurationId);
    }

    public function getBrandApiKeyByBrandId(int $brandId): ?string
    {
        return $this->brandRepository->getBrandKeyByBrandId($brandId);
    }

    /**
     * @param $brandConfigurationValue
     * @param int $brandId
     * @param int $configurationid
     * @return bool
     */
    public function setBrandConfiguration($brandConfigurationValue, int $brandId, int $configurationid): bool
    {
        return $this->brandRepository->updateBrandConfiguration($configurationid, $brandConfigurationValue, $brandId);
    }

    /**
     * @param string $code
     * @param int $brandId
     * @return bool
     */
    public function updateBrandConfigurationWithStripeDto(string $code, int $brandId): bool
    {
        /**
         * First, we need to call Stripe and get for ourselves a StripeDto.
         */
        $stripeDto = $this->stripeRepository->connectStripeAccount($code);

        if (empty($stripeDto)) {
            return false;
        }

        $stripeAccountConfiguration = $this->getConfigurationBySlug('stripeAccount');
        $stripeKeyConfiguration = $this->getConfigurationBySlug('stripePublishableKey');
        $stripeAccessConfiguration = $this->getConfigurationBySlug('stripeAccessToken');

        $updatedStripeAccountConfiguration = $this->brandRepository->updateBrandConfiguration($stripeAccountConfiguration->getId(), $stripeDto->accountId, $brandId);
        $updatedStripePublishableKeyConfiguration = $this->brandRepository->updateBrandConfiguration($stripeKeyConfiguration->getId(), $stripeDto->publishableKey, $brandId);
        $updatedStripeAccessTokenConfiguration = $this->brandRepository->updateBrandConfiguration($stripeAccessConfiguration->getId(), $stripeDto->accessToken, $brandId);

        if ($updatedStripeAccountConfiguration && $updatedStripePublishableKeyConfiguration && $updatedStripeAccessTokenConfiguration) {
            return true;
        }
        return false;
    }

    public function updateBrandConfigurationByBrandConfigurationValue($brandConfigurationsValue, string $brandConfiguration): bool
    {
        return $this->brandRepository->updateBrandConfigurationByBrandCofigurationValue($brandConfigurationsValue, $brandConfiguration);
    }
}
