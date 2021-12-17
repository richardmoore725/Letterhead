<?php

namespace App\Http\Repositories;

use App\Collections\DiscountCodeCollection;
use App\DTOs\DiscountCodeDto;
use App\Http\Repositories\DiscountCodeRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class DiscountCodeRepository implements DiscountCodeRepositoryInterface
{
    public const TABLES_DISCOUNT_CODES = 'discount_codes';

    public function createDiscountCode(DiscountCodeDto $dto): ?DiscountCodeDto
    {
        try {
            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

            $id = $this->insertDiscountCodeIntoDatabase($dto);

            return $this->getDiscountCodeById($id);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function insertDiscountCodeIntoDatabase(DiscountCodeDto $dto): int
    {
        $id = app('db')
          ->table(self::TABLES_DISCOUNT_CODES)
          ->insertGetId($dto->mapDiscountCodeDtoToDatabaseColumns());
        return $id;
    }

    private function getDiscountCodeRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
        ->table(self::TABLES_DISCOUNT_CODES)
        ->where('id', '=', $id)
        ->first();
    }

    private function getDiscountCodeRowFromDatabaseByCode(string $code): ?\stdClass
    {
        return app('db')
        ->table(self::TABLES_DISCOUNT_CODES)
        ->where('discountCode', '=', $code)
        ->whereNull('deleted_at')
        ->first();
    }

    public function getDiscountCodeById(int $id): ?DiscountCodeDto
    {
        try {
            $discountCodeFromDatabase = $this->getDiscountCodeRowFromDatabaseById($id);

            if (empty($discountCodeFromDatabase)) {
                return null;
            }

            $dto = new DiscountCodeDto($discountCodeFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getDiscountCodeByCode(string $code): ?DiscountCodeDto
    {
        try {
            $discountCodeFromDatabase = $this->getDiscountCodeRowFromDatabaseByCode($code);


            if (empty($discountCodeFromDatabase)) {
                return null;
            }

            $dto = new DiscountCodeDto($discountCodeFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getDiscountCodesByChannelId(int $channelId): ?DiscountCodeCollection
    {
        try {
            $codesFromDatabase = app('db')
            ->table(self::TABLES_DISCOUNT_CODES)
            ->where('channelId', '=', $channelId)
            ->whereNull('deleted_at')
            ->get();

            $discountCodes = new DiscountCodeCollection($codesFromDatabase->toArray());

            return $discountCodes;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new DiscountCodeCollection([]);
        }
    }

    public function deleteDiscountCode(int $id): bool
    {
        try {
            return app('db')
            ->table(self::TABLES_DISCOUNT_CODES)
            ->where('id', $id)
            ->update(['deleted_at' => CarbonImmutable::now()]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateDiscountCode(DiscountCodeDto $dto): ?DiscountCodeDto
    {
        $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();
        $dto->deletedAt = null;

        try {
            app('db')
            ->table(self::TABLES_DISCOUNT_CODES)
            ->where('id', $dto->id)
            ->update($dto->mapDiscountCodeDtoToDatabaseColumns());

            $updatedDiscountCode = $this->getDiscountCodeById($dto->id);

            return $updatedDiscountCode;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
