<?php

namespace App\Http\Repositories;

use App\Collections\ChannelConfigurationCollection;
use App\Collections\ChannelCollection;
use App\DTOs\BrandKeyDto;
use App\DTOs\ChannelConfigurationDto;
use App\DTOs\ChannelDto;
use App\DTOs\ConfigurationDto;
use App\Http\Response;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class ChannelRepository implements ChannelRepositoryInterface
{
    public const TABLE_CHANNELS = 'channels';
    public const TABLE_CHANNEL_CONFIGURATIONS = 'channel_configurations';
    public const TABLE_CONFIGURATIONS = 'configurations';

    /**
     * Create a channel only, not a channel _with_ configurations,
     * and return the newly created ID on success.
     *
     * @param ChannelDto $dto
     * @return int|null Newly created ID.
     */
    public function createChannel(ChannelDto $dto): ?ChannelDto
    {
        try {
            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();
            $channelId = $this->insertChannelIntoDatabase($dto);
            return $this->getChannelById($channelId);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * Create a ChannelConfiguration and return its newly created ID, on success.
     *
     * @param ChannelConfigurationDto $dto
     * @return int|null New ChannelConfiguration ID
     */
    public function createChannelConfiguration(ChannelConfigurationDto $dto): ?int
    {
        try {
            return $this->insertChannelConfigurationIntoDatabase($dto);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * This will locate and remove a Channel from the database. Because related
     * ChannelConfigurations will be deleted by cascade when a Channel is axed,
     * this method will clear-out all of those too : ).
     *
     * @param ChannelDto $dto
     * @return bool
     */
    public function deleteChannel(ChannelDto $dto): bool
    {
        try {
            return app('db')
                ->table(self::TABLE_CHANNELS)
                ->where('id', $dto->id)
                ->update([
                    'deleted_at' => CarbonImmutable::now(),
                ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $slug
     * @return ChannelDto|null
     */
    public function getChannelBySlug(string $slug): ?ChannelDto
    {
        try {
            $channelFromDatabase = $this->getChannelRowFromDatabaseBySlug($slug);

            if (empty($channelFromDatabase)) {
                return null;
            }

            $dto = new ChannelDto($this->getChannelRowFromDatabaseBySlug($slug));
            $dto->channelConfigurations = $this->getChannelConfigurationsFromDatabaseByChannelId($dto->id);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * Get the first channel found given a brand's. We'll experiment using the new Response class
     * to send the right data. On success, we'll return the Dto. On failure, we'll return
     * a useful message.
     *
     * @param string $key
     * @return Response
     */
    public function getChannelByBrandApiKey(string $key): Response
    {
        try {
            $brandTable = BrandRepository::TABLE_BRAND;
            $channelTable = self::TABLE_CHANNELS;
            $keyTable = BrandRepository::TABLE_BRAND_KEYS;

            $queryResult = app('db')
                ->table($keyTable)
                ->join($brandTable, "{$keyTable}.brandId", '=', "{$brandTable}.id")
                ->join($channelTable, "{$brandTable}.id", '=', "{$channelTable}.brandId")
                ->where('key', '=', $key)
                ->select(
                    "{$channelTable}.id",
                    "{$channelTable}.title",
                    "{$channelTable}.created_at",
                    "{$channelTable}.updated_at",
                    "{$channelTable}.brandId",
                    "{$channelTable}.channelSlug",
                    "{$channelTable}.channelDescription",
                    "{$channelTable}.channelImage",
                    "{$channelTable}.deleted_at",
                    "{$channelTable}.channelHorizontalLogo",
                    "{$channelTable}.channelSquareLogo",
                    "{$keyTable}.isActive"
                )
                ->get()
                ->first();

            /**
             * The queryResult will be empty due to the join if either the
             * key, a corresponding brand, or a corresponding channel cannot be found. In the
             * future if we need we can separate these lookups for more detailed error
             * conditioning, but for now we can be a little less detailed.
             */

            if (empty($queryResult)) {
                return new Response('There is either no corresponding brand or channel associated with this key.', 404);
            }

            $partialKeyDto = new BrandKeyDto($queryResult);
            $brandHasInactiveKey = !$partialKeyDto->isActive;

            if ($brandHasInactiveKey) {
                return new Response('This key is invalid', 401);
            }

            $channelDto = new ChannelDto($queryResult);
            return new Response('', 200, $channelDto);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new Response('', 500);
        }
    }

    public function getChannelsByBrandId(int $brandId): array
    {
        try {
            $channels = $this->toChannelDtoArray($this->getChannelsByBrandIdFromDatabase($brandId));
            $channelsWithConfigurations = $this->getChannelConfigurationsForMultipleChannelsByChannelId($channels);

            return $channelsWithConfigurations;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());

            return [];
        }
    }

    /**
     * Loops through an array of ChannelDtos and fetch all of their configurations, returning
     * an array of those same ChannelDtos now fully populated. Thsi is probably an unnecessary
     * step and is worth an eyeball if we want to refactor for funsies. Does the job for now.
     *
     * @param array $arrayOfChannelDtos
     * @return array
     */
    private function getChannelConfigurationsForMultipleChannelsByChannelId(array $arrayOfChannelDtos): array
    {
        $channels = array_map(function (ChannelDto $channelDto) {
            $channelDto->channelConfigurations = $this->getChannelConfigurationsFromDatabaseByChannelId($channelDto->id);

            return $channelDto;
        }, $arrayOfChannelDtos);

        return $channels;
    }


    /**
     * Gets a channel by its ID and all corresponding ChannelConfigurations.
     *
     * @param int $id
     * @return ChannelDto|null
     */
    public function getChannelById(int $id): ?ChannelDto
    {
        try {
            $channelFromDatabase = $this->getChannelRowFromDatabaseById($id);

            if (empty($channelFromDatabase)) {
                return null;
            }

            $dto = new ChannelDto($channelFromDatabase);
            $dto->channelConfigurations = $this->getChannelConfigurationsFromDatabaseByChannelId($id);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * Use the Lumen query builder to find the Channel by its id in the database.
     *
     * @param int $id
     * @return null|\stdClass
     * @throws \Exception
     */
    private function getChannelRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
            ->table(self::TABLE_CHANNELS)
            ->find($id);
    }

    /**
     * @param string $slug
     * @return null|\stdClass
     * @throws \Exception
     */
    private function getChannelRowFromDatabaseBySlug(string $slug): ?\stdClass
    {
        return app('db')
            ->table(self::TABLE_CHANNELS)
            ->where('channelSlug', '=', $slug)
            ->first();
    }

    /**
     * @param int $channelId
     * @return Collection collection of ChannelConfiguration objects
     * @throws \Exception
     */
    private function getChannelConfigurationsFromDatabaseByChannelId(int $channelId): Collection
    {
        $channelConfigurationsTable = self::TABLE_CHANNEL_CONFIGURATIONS;
        $configurationsTable = self::TABLE_CONFIGURATIONS;

        $configurations = app('db')
            ->table($channelConfigurationsTable)
            ->join($configurationsTable, "{$channelConfigurationsTable}.configurationId", '=', "{$configurationsTable}.id")
            ->where('channelId', '=', $channelId)
            ->select("{$channelConfigurationsTable}.*", "{$configurationsTable}.configurationName", "{$configurationsTable}.configurationSlug", "{$configurationsTable}.dataType")
            ->get();

        return new ChannelConfigurationCollection($configurations);
    }

    /**
     * Use the Lumen query builder to retrieve all channels by their Brand ID.
     *
     * @param int $brandId
     * @return Collection
     * @throws \Exception
     */
    private function getChannelsByBrandIdFromDatabase(int $brandId): Collection
    {
        return app('db')
            ->table(self::TABLE_CHANNELS)
            ->where('brandId', '=', $brandId)
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * This method is responsible for transforming the ChannelDto into
     * an array then inserting them through Lumen's query builder.
     *
     * @param ChannelDto $dto
     * @return bool
     * @throws \Exception
     */
    private function insertChannelIntoDatabase(ChannelDto $dto): int
    {
        $channelId = app('db')
            ->table(self::TABLE_CHANNELS)
            ->insertGetId($dto->mapChannelDtoToDatabaseColumns());

        return $channelId;
    }

    /**
     * Given a ChannelConfigurationDto, this method will use Lumen's query builder to
     * insert the data.
     *
     * @param ChannelConfigurationDto $dto
     * @return int
     * @throws \Exception
     */
    private function insertChannelConfigurationIntoDatabase(ChannelConfigurationDto $dto): int
    {
        $channelColumnValues = $this->mapChannelConfigurationDtoToDatabaseColumns($dto);

        return app('db')
            ->table(self::TABLE_CHANNEL_CONFIGURATIONS)
            ->insertGetId($channelColumnValues);
    }


    /**
     * This will insert updated values into an existing channel row using Lumen's
     * query builder.
     *
     * @param ChannelDto $dto
     * @return bool
     * @throws \Exception
     */
    private function insertChannelUpdatesInDatabase(ChannelDto $dto): bool
    {
        return app('db')
            ->table(self::TABLE_CHANNELS)
            ->where('id', $dto->id)
            ->update($dto->mapChannelDtoToDatabaseColumns());
    }

    /**
     * This method maps properties from the Dto to their relevant columns in the
     * database.
     *
     * @param ChannelConfigurationDto $dto
     * @return array
     */
    private function mapChannelConfigurationDtoToDatabaseColumns(ChannelConfigurationDto $dto): array
    {
        return [
            'channelConfigurationValue' => $dto->channelConfigurationValue,
            'channelId' => $dto->channelId,
            'configurationId' => $dto->configurationId,
            'id' => $dto->id,
        ];
    }

    /**
     * Transforms the results from a database into ChannelDtos.
     *
     * @param $resultSet
     * @return array
     */
    private function toChannelDtoArray($resultSet): array
    {
        $dtoArray = [];

        foreach ($resultSet as $object) {
            $dtoArray[] = new ChannelDto($object);
        }

        return $dtoArray;
    }

    public function updateChannel(ChannelDto $dto): ?ChannelDto
    {
        $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

        try {
            $this->insertChannelUpdatesInDatabase($dto);
            return $this->getChannelById($dto->id);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function updateChannelConfiguration(int $channelId, $channelConfigurationValue, int $configurationId): bool
    {
        try {
            $this->updateChannelConfigurationValue($channelId, $channelConfigurationValue, $configurationId);
            return true;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * @return bool
     * @see https://laravel.com/docs/5.8/queries#updates
     */
    private function updateChannelConfigurationValue(int $channelId, $channelConfigurationValue, int $configurationId): bool
    {
        return app('db')
            ->table(self::TABLE_CHANNEL_CONFIGURATIONS)
            ->updateOrInsert(
                [
                    'channelId' => $channelId,
                    'configurationId' => $configurationId,
                ],
                [
                    'channelConfigurationValue' => $channelConfigurationValue,
                ]
            );
    }

    public function getChannels(): ChannelCollection
    {
        try {
            $channelsDatabaseResult = app('db')
                ->table(self::TABLE_CHANNELS)
                ->get();

            $channelDtos = array_map(function (object $channelDatabaseObject) {
                $channelDto = new ChannelDto($channelDatabaseObject);
                $channelDto->channelConfigurations = $this->getChannelConfigurationsFromDatabaseByChannelId($channelDto->id);

                return $channelDto;
            }, $channelsDatabaseResult->toArray());

            return new ChannelCollection($channelDtos);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new ChannelCollection([]);
        }
    }

    /**
     * Get an array of channelDtos whose `autoUpdateChannelStatsFromMailchimp` is true
     *
     * @return Collection
     * @throws \Exception
     */
    public function getChannelsThatAutoSyncListStats(): ChannelCollection
    {
        $channelConfigurationsTable = self::TABLE_CHANNEL_CONFIGURATIONS;
        $configurationsTable = self::TABLE_CONFIGURATIONS;
        $channelsTable = self::TABLE_CHANNELS;

        try {
            $channelObjects = app('db')
                ->table($channelConfigurationsTable)
                ->join($channelsTable, "{$channelConfigurationsTable}.channelId", '=', "{$channelsTable}.id")
                ->join($configurationsTable, "{$channelConfigurationsTable}.configurationId", '=', "{$configurationsTable}.id")
                ->where('configurationSlug', '=', 'autoUpdateChannelStatsFromMailchimp')
                ->where('channelConfigurationValue', '=', 1)
                ->select("{$channelsTable}.*")
                ->get();

            $channelDtos = array_map(function (object $channelObject) {
                $dto = new ChannelDto($channelObject);
                $dto->channelConfigurations = $this->getChannelConfigurationsFromDatabaseByChannelId($dto->id);

                return $dto;
            }, $channelObjects->toArray());

            return new ChannelCollection($channelDtos);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new ChannelCollection([]);
        }
    }
}
