<?php

namespace App\Http\Repositories;

use App\Collections\EmailCollection;
use App\DTOs\EmailDto;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class EmailRepository implements EmailRepositoryInterface
{
    public const TABLE_EMAILS = 'emails';

    public function createEmail(EmailDto $dto): ?EmailDto
    {
        try {
            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

            $id = $this->insertEmailIntoDatabase($dto);
            return $this->getEmailById($id);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function insertEmailIntoDatabase(EmailDto $dto): int
    {
        $id = app('db')
          ->table(self::TABLE_EMAILS)
          ->insertGetId($dto->mapEmailDtoToDatabaseColumns());
        return $id;
    }

    public function getEmailById(int $id): ?EmailDto
    {
        try {
            $emailFromDatabase = $this->getEmailRowFromDatabaseById($id);

            if (empty($emailFromDatabase)) {
                return null;
            }

            $dto = new EmailDto($emailFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function getEmailRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
        ->table(self::TABLE_EMAILS)
        ->where('id', '=', $id)
        ->first();
    }

    public function deleteEmail(EmailDto $dto): bool
    {
        try {
            return app('db')
            ->table(self::TABLE_EMAILS)
            ->where('id', $dto->id)
            ->update(['deleted_at' => CarbonImmutable::now()]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateEmail(EmailDto $dto): ?EmailDto
    {
        $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();
        $dto->deletedAt = null;

        try {
            app('db')
            ->table(self::TABLE_EMAILS)
            ->where('id', $dto->id)
            ->update($dto->mapEmailDtoToDatabaseColumns());

            $updatedEmail = $this->getEmailById($dto->id);

            return $updatedEmail;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getEmailsByChannelId(int $channelId): ?EmailCollection
    {
        try {
            $emailsFromDataBase =  app('db')
            ->table(self::TABLE_EMAILS)
            ->where('channelId', '=', $channelId)
            ->whereNull('deleted_at')
            ->get();

            $emails = new EmailCollection($emailsFromDataBase);
            return $emails;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
