<?php

namespace App\Http\Repositories;

use App\Collections\TransactionalEmailCollection;
use App\DTOs\TransactionalEmailDto;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class TransactionalEmailRepository implements TransactionalEmailRepositoryInterface
{
    public const TABLE_TRANSACTIONAL_EMAILS = 'transactional_email';
    public const TABLE_PLATFORM_EVENTS = 'platform_events';

    public function createTransactionalEmail(TransactionalEmailDto $dto): ?TransactionalEmailDto
    {
        try {
            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

            $id = $this->insertTransactionalEmailIntoDatabase($dto);
            return $this->getTransactionalEmailById($id);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function insertTransactionalEmailIntoDatabase(TransactionalEmailDto $dto): int
    {
        $id = app('db')
          ->table(self::TABLE_TRANSACTIONAL_EMAILS)
          ->insertGetId($dto->mapTransactionalEmailDtoToDatabaseColumns());
        return $id;
    }

    public function getTransactionalEmailById(int $id): ?TransactionalEmailDto
    {
        try {
            $transactionaEmailFromDatabase = $this->getTransactionaEmailRowFromDatabaseById($id);

            if (empty($transactionaEmailFromDatabase)) {
                return null;
            }

            $dto = new TransactionalEmailDto($transactionaEmailFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function getTransactionaEmailRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
          ->table(self::TABLE_TRANSACTIONAL_EMAILS)
          ->where('id', '=', $id)
          ->first();
    }

    public function deleteTransactionalEmail(TransactionalEmailDto $dto): bool
    {
        try {
            return app('db')
            ->table(self::TABLE_TRANSACTIONAL_EMAILS)
            ->where('id', $dto->id)
            ->update(['deleted_at' => CarbonImmutable::now()]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateTransactionalEmail(TransactionalEmailDto $dto): ?TransactionalEmailDto
    {
        $dto->updatedAt = CarbonImmutable::now();
        $dto->deletedAt = null;

        try {
            app('db')
            ->table(self::TABLE_TRANSACTIONAL_EMAILS)
            ->where('id', $dto->id)
            ->update($dto->mapTransactionalEmailDtoToDatabaseColumns());

            $updatedTransactionalEmail = $this-> getTransactionalEmailById($dto->id);

            return $updatedTransactionalEmail;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getTransactionalEmailsByChannelId(int $channelId): ?TransactionalEmailCollection
    {
        try {
            $transactionalEmailsFromDatabase = app('db')
            ->table(self::TABLE_TRANSACTIONAL_EMAILS)
            ->where('channelId', '=', $channelId)
            ->whereNull('deleted_at')
            ->get();

            $transactionaEmails = new TransactionalEmailCollection($transactionalEmailsFromDatabase);

            return $transactionaEmails;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getTransactionalEmailByChannelAndEventSlug(int $channelId, string $eventSlug): ?TransactionalEmailDto
    {
        try {
            $transactionalEmailTable = self::TABLE_TRANSACTIONAL_EMAILS;
            $platformEventTable = self::TABLE_PLATFORM_EVENTS;

            $transactionalEmailFromDatabase = app('db')
            ->table($transactionalEmailTable)
            ->join($platformEventTable, "{$transactionalEmailTable}.eventId", '=', "{$platformEventTable}.id")
            ->where('eventSlug', '=', $eventSlug)
            ->where('channelId', '=', $channelId)
            ->select("{$transactionalEmailTable}.name", "{$transactionalEmailTable}.description", "{$transactionalEmailTable}.brandId", "{$transactionalEmailTable}.channelId", "{$transactionalEmailTable}.emailId", "{$transactionalEmailTable}.eventId", "{$transactionalEmailTable}.isActive", "{$transactionalEmailTable}.id")
            ->first();

            if (empty($transactionalEmailFromDatabase)) {
                return null;
            }

            $transactionalEmail = new TransactionalEmailDto($transactionalEmailFromDatabase);

            return $transactionalEmail;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
