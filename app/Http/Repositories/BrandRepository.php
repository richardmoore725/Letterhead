<?php

namespace App\Http\Repositories;

use App\Collections\BrandConfigurationCollection;
use App\DTOs\BrandDto;
use App\DTOs\BrandKeyDto;
use App\Collections\BrandCollection;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use RandomLib\Factory;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class BrandRepository implements BrandKeyRepositoryInterface, BrandRepositoryInterface
{
    public const TABLE_BRAND = 'brands';
    public const TABLE_BRAND_CONFIGURATIONS = 'brand_configurations';
    public const TABLE_BRAND_KEYS = 'brand_keys';
    public const TABLE_CONFIGURATIONS = 'configurations';

    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function createBrandFeaturesAndConfigurations(BrandDto $dto): ?BrandDto
    {
        try {
            app('db')->beginTransaction();

            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();
            $dto->id = $this->insertBrand($dto);
            $this->createBrandKey($dto->id);

            $brand = $this->getBrandById($dto->id);

            app('db')->commit();

            return $brand;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            app('db')->rollBack();
            return null;
        }
    }

    /**
     * We use `createBrandKey` to generate a new BrandKey for a specific brand, which
     * which we - assuming all goes well - will return.
     *
     * @param int $brandId
     * @return null|string
     * @throws \Exception
     */
    private function createBrandKey(int $brandId): ?string
    {
        $newlyGeneratedBrandKeyId = $this->generateAndInsertANewBrandKeyIntoDatabase($brandId);
        return $this->getBrandKeyByBrandId($newlyGeneratedBrandKeyId);
    }

    /**
     * This will delete a brand, and because related Brand Configurations,
     * Brand Features, and Channels will delete on cascade (see the schema),
     * it will delete all of those too.
     *
     * @param BrandDto $dto
     * @return bool
     */
    public function deleteBrand(BrandDto $dto): bool
    {
        try {
            return app('db')->table(self::TABLE_BRAND)
                    ->where('id', $dto->id)
                    ->delete();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getBrandById(int $brandId): ?BrandDto
    {
        $brand = $this->getBrandDtoById($brandId);
        return empty($brand) ? null : $this->getBrandWithConfigurationsAndFeaturesByBrandDto($brand);
    }

    public function getBrandBySlug(string $brandSlug): ?BrandDto
    {
        $brand = $this->getBrandDtoBySlug($brandSlug);
        return empty($brand) ? null : $this->getBrandWithConfigurationsAndFeaturesByBrandDto($brand);
    }

    /**
     * This method creates and returns a new `BrandKeyDto` that has a BrandId and
     * a medium-secure randomly generated string as its key.
     *
     * @param int $brandId
     * @return BrandKeyDto
     */
    private function generateBrandKeyDtoWithBrandId(int $brandId): BrandKeyDto
    {
        $brandKeyDto = new BrandKeyDto();
        $brandKeyDto->brandId = $brandId;
        $brandKeyDto->key = $this->generateRandomString();

        return $brandKeyDto;
    }

    /**
     * Here, we generate a BrandKeyDto that is includes a new, 64-digit, medium
     * strength key string, then insert that into the database. We return its newly
     * created ID.
     *
     * @param int $brandId
     * @return int Newly created BrandKey ID
     * @throws \Exception
     */
    private function generateAndInsertANewBrandKeyIntoDatabase(int $brandId): int
    {
        $dto = $this->generateBrandKeyDtoWithBrandId($brandId);
        return $this->inserBrandKeyIntoDatabase($dto);
    }

    /**
     * Here, we generate a medium-strength, 64-digit random string that we can use as
     * a key.
     *
     * @return string
     */
    private function generateRandomString(): string
    {
        $charactersToComposeKey = 'abcdefghiklmnopqrstuvwxyz0123456789';
        $randomStringFactory = new Factory();
        $randomStringGenerator = $randomStringFactory->getMediumStrengthGenerator();

        return $randomStringGenerator->generateString(64, $charactersToComposeKey);
    }

    private function getBrandConfigurationsByBrandId(int $brandId): BrandConfigurationCollection
    {
        $brandConfigurationsTables = self::TABLE_BRAND_CONFIGURATIONS;
        $configurationsTable = self::TABLE_CONFIGURATIONS;

        $results = app('db')
            ->table(self::TABLE_BRAND_CONFIGURATIONS)
            ->join($configurationsTable, "{$brandConfigurationsTables}.configurationId", '=', "{$configurationsTable}.id")
            ->where('brandId', '=', $brandId)
            ->select("{$brandConfigurationsTables}.*", "{$configurationsTable}.configurationName", "{$configurationsTable}.configurationSlug", "{$configurationsTable}.dataType")
            ->get();

        return new BrandConfigurationCollection($results);
    }

    private function getBrandDtoById(int $brandId): ?BrandDto
    {
        try {
            $result = app('db')
                ->table(self::TABLE_BRAND)
                ->find($brandId);

            return empty($result) ? null : new BrandDto($result);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getBrandDtoBySlug(string $brandSlug): ?BrandDto
    {
        try {
            $result = app('db')
                ->table(self::TABLE_BRAND)
                ->where('brandSlug', '=', $brandSlug)
                ->first();

            return empty($result) ? null : new BrandDto($result);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Given a `brandId`, we'll find the first key that matches and return just
     * the value from the `key` column - rather than transform the whole shebang
     * into a Dto, which we [presently] don't need.
     *
     * @param int $brandId
     * @see BrandKeyDto
     * @return string
     */
    public function getBrandKeyByBrandId(int $brandId): ?string
    {
        try {
            return app('db')
                ->table(self::TABLE_BRAND_KEYS)
                ->where('brandId', $brandId)
                ->value('key');
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function getBrandWithConfigurationsAndFeaturesByBrandDto(BrandDto $dto): BrandDto
    {
        $brandId = $dto->id;

        $dto->brandConfigurations = $this->getBrandConfigurationsByBrandId($brandId);

        return $dto;
    }

    /**
     * @return BrandDto[]
     */
    public function getBrands(): BrandCollection
    {
        $brandsDatabaseResult = app('db')
            ->table(self::TABLE_BRAND)
            ->get();

        $brandDtos = array_map(function (object $brandDatabaseObject) {
            $brandDto = new BrandDto($brandDatabaseObject);
            $brandDto->brandConfigurations = $this->getBrandConfigurationsByBrandId($brandDto->id);
            $brandDto->channels = $this->channelRepository->getChannelsByBrandId($brandDto->id);

            return $brandDto;
        }, $brandsDatabaseResult->toArray());

        return new BrandCollection($brandDtos);
    }

    public function getBrandsByIds(array $brandIds): BrandCollection
    {
        try {
            $rowsOfBrands = app('db')
                ->table(self::TABLE_BRAND)
                ->whereIn('id', $brandIds)
                ->get();

            $brandDtos = array_map(function (object $brandDatabaseObject) {
                $brandDto = new BrandDto($brandDatabaseObject);
                $brandDto->brandConfigurations = $this->getBrandConfigurationsByBrandId($brandDto->id);
                $brandDto->channels = $this->channelRepository->getChannelsByBrandId($brandDto->id);

                return $brandDto;
            }, $rowsOfBrands->toArray());

            return new BrandCollection($brandDtos);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return new BrandCollection();
        }
    }

    /**
     * We'll use Lumen's `insertGetId` method to add the new brand
     * to the database, then return that newly-added brand's ID.
     *
     * @param BrandDto $dto
     * @return int
     */
    private function insertBrand(BrandDto $dto): int
    {
        return app('db')
            ->table(self::TABLE_BRAND)
            ->insertGetId($dto->mapToDatabaseColumns());
    }

    /**
     * This method inserts a new Brand Key into the database, then returns its
     * ID - or, worst-case scenario, throws an exception.
     *
     * @param BrandKeyDto $dto
     * @return int
     * @throws \Exception
     */
    private function inserBrandKeyIntoDatabase(BrandKeyDto $dto): int
    {
        return app('db')
            ->table(self::TABLE_BRAND_KEYS)
            ->insertGetId($dto->convertToArrayOfDatabaseColumns());
    }

    /**
     * This will update only the brand, not the config
     *
     * @param BrandDto $dto
     * @return BrandDto|null
     */
    public function updateBrand(BrandDto $dto): ?BrandDto
    {
        $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

        try {
            app('db')->beginTransaction();

            app('db')
                ->table(self::TABLE_BRAND)
                ->where('id', $dto->id)
                ->update($dto->mapToDatabaseColumns());

            $updatedBrand = $this->getBrandById($dto->id);

            app('db')->commit();

            return $updatedBrand;
        } catch (\Exception $e) {
            app('db')->rollBack();
            return null;
        }
    }

    public function updateBrandConfiguration(
        int $configurationId,
        $brandConfigurationValue,
        int $brandId
    ): bool {
        try {
            app('db')
            ->table(self::TABLE_BRAND_CONFIGURATIONS)
            ->updateOrInsert(
                ['configurationId' => $configurationId, 'brandId' => $brandId],
                ['brandConfigurationValue' => $brandConfigurationValue]
            );
            return true;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return false;
        }
    }

    public function updateBrandConfigurationByBrandCofigurationValue($brandConfigurationValue, string $brandConfiguration): bool
    {
        try {
            app('db')
                ->table(self::TABLE_BRAND_CONFIGURATIONS)
                ->where('brandConfigurationValue', $brandConfigurationValue)
                ->update(['brandConfigurationValue' => $brandConfiguration]);

            return true;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return false;
        }
    }
}
