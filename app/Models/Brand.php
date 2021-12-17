<?php

namespace App\Models;

use App\Collections\BrandConfigurationCollection;
use App\DTOs\BrandDto;
use Illuminate\Support\Facades\Storage;

class Brand
{
    /**
     * @var BrandConfigurationCollection
     */
    private $brandConfigurations;

    /**
     * @var string
     */
    private $brandHorizontalLogo;
    private $brandName;
    private $brandSlug;

    /**
     * @var string
     */
    private $brandSquareLogo;
    private $channels;
    private $createdAt;
    private $id;
    private $updatedAt;

    public function __construct(BrandDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->brandConfigurations = $dto->brandConfigurations;
        $this->brandHorizontalLogo = $dto->brandHorizontalLogo;
        $this->brandName = $dto->brandName;
        $this->brandSlug = $dto->brandSlug;
        $this->brandSquareLogo = $dto->brandSquareLogo;
        $this->channels = $this->toChannelModelArray($dto->channels);
        $this->createdAt = $dto->createdAt;
        $this->id = $dto->id;
        $this->updatedAt = $dto->updatedAt;
    }

    /**
     * This will convert our `Brand` object, with a bunch of private properties,
     * to an array of publicly visible properties we choose. In this way, we can control
     * what and how we expose data to the API endpoints.
     *
     * @return array
     */
    public function convertToArray(): array
    {
        $channels = array_map(function (Channel $channel) {
            return $channel->convertToArray();
        }, $this->channels);

        return [
            'advertisingRevenueShare' => $this->brandConfigurations->getAdvertisingRevenueShare(),
            'brandContactAddress__city' => $this->brandConfigurations->getBrandContactAddress__city(),
            'brandContactAddress__postal' => $this->brandConfigurations->getBrandContactAddress__postal(),
            'brandContactAddress__state' => $this->brandConfigurations->getBrandContactAddress__state(),
            'brandContactAddress__street' => $this->brandConfigurations->getBrandContactAddress__street(),
            'brandContactEmail' => $this->brandConfigurations->getBrandContactEmail(),
            'brandContactName' => $this->brandConfigurations->getBrandContactName(),
            'brandContactPhone' => $this->brandConfigurations->getBrandContactPhone(),
            'brandHorizontalLogo' => $this->getBrandHorizontalLogo(),
            'brandName' => $this->brandName,
            'brandSlug' => $this->brandSlug,
            'brandSquareLogo' => $this->getBrandSquareLogo(),
            'brandUrl' => $this->brandConfigurations->getBrandUrl(),
            'channels' => $channels,
            'createdAt' => $this->createdAt,
            'id' => $this->id,
            'stripeAccount' => $this->brandConfigurations->getStripeAccount(),
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function convertToDto(): BrandDto
    {
        $dto = new BrandDto();
        $dto->brandConfigurations = $this->brandConfigurations;
        $dto->brandHorizontalLogo = $this->brandSquareLogo;
        $dto->brandName = $this->brandName;
        $dto->brandSlug = $this->brandSlug;
        $dto->brandSquareLogo = $this->brandSquareLogo;
        $dto->createdAt = $this->createdAt;
        $dto->id = $this->id;
        $dto->updatedAt = $this->updatedAt;

        return $dto;
    }

    public function getBrandConfigurations(): BrandConfigurationCollection
    {
        return $this->brandConfigurations;
    }

    public function getBrandHorizontalLogo(): string
    {
        return empty($this->brandHorizontalLogo) ?
            '' :
            Storage::url($this->brandHorizontalLogo);
    }

    public function getBrandSquareLogo(): string
    {
        return empty($this->brandSquareLogo) ?
            '' :
            Storage::url($this->brandSquareLogo);
    }

    /**
     * @return array
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getBrandName(): string
    {
        return $this->brandName;
    }

    public function getBrandSlug(): string
    {
        return $this->brandSlug;
    }

    /**
     * Returns an array of Validation rules, which can be used to pass to a
     * Validator.
     *
     * @return array
     * @see https://lumen.laravel.com/docs/5.8/validation
     * @see https://laravel.com/docs/5.8/validation#available-validation-rules
     */

    public static function getValidationRules(): array
    {
        return [
            'brandName' => 'required|string',
            'brandHorizontalLogo' => 'nullable|file',
            'brandSlug' => 'required|string',
            'brandSquareLogo' => 'nullable|file',
        ];
    }

    public function setBrandHorizontalLogo(string $brandHorizontalLogo): void
    {
        $this->brandHorizontalLogo = $brandHorizontalLogo;
    }

    public function setBrandName(string $brandName): void
    {
        $this->brandName = $brandName;
    }

    public function setBrandSlug(string $brandSlug): void
    {
        $this->brandSlug = $brandSlug;
    }

    public function setBrandSquareLogo(string $brandSquareLogo): void
    {
        $this->brandSquareLogo = $brandSquareLogo;
    }

    /**
     * @param array $channels
     */
    public function setChannels(array $channels): void
    {
        $this->channels = $channels;
    }

    private function toChannelModelArray(array $arrayOfChannels): array
    {
        $modelArray = [];

        foreach ($arrayOfChannels as $dto) {
            $modelArray[] = new Channel($dto);
        }

        return $modelArray;
    }
}
