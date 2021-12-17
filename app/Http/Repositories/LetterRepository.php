<?php

namespace App\Http\Repositories;

use App\Collections\LetterCollection;
use App\Collections\LetterPartCollection;
use App\Collections\LettersEmailsCollection;
use App\Collections\LettersUsersCollection;
use App\DTOs\LetterDto;
use App\DTOs\LetterPartDto;
use App\DTOs\LettersUsersDto;
use App\Models\LetterPart;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Support\Facades\DB;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class LetterRepository implements LetterRepositoryInterface
{
    public const TABLE_LETTER_PARTS = 'letter_parts';
    public const TABLE_LETTERS = 'letters';
    public const TABLE_LETTERS_EMAILS = 'letters_emails';
    public const TABLE_LETTERS_USERS = 'letters_users';

    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    private function cacheLetterDto(LetterDto $dto, string $key): bool
    {
        $cacheExpiration = CarbonImmutable::now()->addMonth()->toDateTime();
        return $this->cache->put("{$key}", $dto, $cacheExpiration);
    }

    private function cacheLettersByChannel(LetterCollection $letters, string $key): bool
    {
        $cacheExpiration = CarbonImmutable::now()->addMonth()->toDateTime();
        return $this->cache->put("{$key}", $letters, $cacheExpiration);
    }

    public function createLetter(
        array $arrayOfAuthorIds,
        array $arrayOfEmptyLetterParts,
        LetterDto $letterToCreate,
        string $timeStampForCreation
    ): ?LetterDto {
        try {
            app('db')->beginTransaction();

            $letterId = app('db')->table(self::TABLE_LETTERS)->insertGetId($letterToCreate->mapChannelDtoToDatabaseColumns());
            $lettersUsersCollection = $this->generateLettersUsersCollection($arrayOfAuthorIds, $letterId);
            $letterPartCollection = $this->generateLetterPartsCollectionFromNewLetterAndArrayOfParts($arrayOfEmptyLetterParts, $timeStampForCreation, $letterId);

            app('db')->table(self::TABLE_LETTERS_USERS)->insert($lettersUsersCollection->getLettersUsersMappedToDatabaseColumns());
            app('db')->table(self::TABLE_LETTER_PARTS)->insert($letterPartCollection->getLetterPartsMappedToDatabaseColumns());
            app('db')->commit();

            $this->forgetChannelLettersCache($letterToCreate->channelId);

            /**
             * We'll fetch the letter from its database. Since it has no emails, we'll not try to
             * fetch and then store it in cache.
             */
            $letterDto = $this->getLetterById($letterId);

            return $letterDto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            app('db')->rollBack();
            return null;
        }
    }

    private function forgetChannelLettersCache(int $channelId)
    {
        $cacheKey = $this->getLettersCacheKeyByChannel($channelId);
        return $this->cache->forget($cacheKey);
    }

    private function forgetLetterCache(int $letterId)
    {
        $cacheKey = $this->getLetterCacheKeyByLetterId($letterId);
        return $this->cache->forget($cacheKey);
    }

    private function generateLetterPartsCollectionFromNewLetterAndArrayOfParts(array $arrayOfLetterParts, string $createdAt, int $letterId): LetterPartCollection
    {
        $arrayOfLetterPartDtos = array_map(function (LetterPart $part) use ($createdAt, $letterId) {
            $part->setCreatedAt($createdAt);
            $part->setLetterId($letterId);
            $part->setUpdatedAt($createdAt);

            return new LetterPartDto(null, $part);
        }, $arrayOfLetterParts);

        return new LetterPartCollection($arrayOfLetterPartDtos);
    }

    /**
     * @param array $arrayOfAuthorIds
     * @param int $letterId
     * @return LettersUsersCollection
     */
    private function generateLettersUsersCollection(array $arrayOfAuthorIds, int $letterId): LettersUsersCollection
    {
        $arrayOfLettersUsersDtos = array_map(function ($authorId) use ($letterId) {
            $lettersUsersDto = new LettersUsersDto();

            /**
             * Just in case the authorId has come over the wire as a string, we'll
             * play it safe and ensure that it is case as an integer.
             */
            $lettersUsersDto->userId = (int) $authorId;
            $lettersUsersDto->letterId = $letterId;

            return $lettersUsersDto;
        }, $arrayOfAuthorIds);

        return new LettersUsersCollection($arrayOfLettersUsersDtos);
    }

    private function getAuthorsByLetterId(int $letterId): LettersUsersCollection
    {
        try {
            $authorRows = app('db')->table(self::TABLE_LETTERS_USERS)
                ->where('letterId', '=', $letterId)
                ->get();

            return new LettersUsersCollection($authorRows);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new LettersUsersCollection([]);
        }
    }

    private function getEmailsByLetterId(int $letterId): LettersEmailsCollection
    {
        try {
            $emailRows = app('db')->table(self::TABLE_LETTERS_EMAILS)
                ->where('letterId', '=', $letterId)
                ->get();

            return new LettersEmailsCollection($emailRows);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new LettersEmailsCollection([]);
        }
    }

    private function getLetterCacheKeyByLetterId(int $letterId): string
    {
        return "letter_{$letterId}";
    }

    private function getLettersCacheKeyByChannel(int $channelId): string
    {
        return "channel_{$channelId}_letters";
    }

    /**
     * Deleting a letter will also trash all corresponding LetterParts
     *
     * @param Letter $letter
     * @return bool
     */
    public function deleteLetter(LetterDto $dto): bool
    {
        try {
            $this->forgetLetterCache($dto->id);
            $this->forgetChannelLettersCache($dto->channelId);

            return app('db')->table(self::TABLE_LETTERS)
                    ->where('id', $dto->id)
                    ->update([
                        'deleted_at' => CarbonImmutable::now(),
                    ]);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * Set the letter's status
     *
     * @param int $letterId
     * @param int $status
     * @return bool
     */
    public function setLetterStatus(int $letterId, int $status): bool
    {
        try {
            $letters = app('db')->table(self::TABLE_LETTERS)
                ->where('id', $letterId)
                ->update(['status' => $status]);

            return true;
        } catch (\Exception $e) {
            false;
        }
    }

    /**
     * Grab all letters with the given status and a publish date <= the given cutoff
     *
     * @param int $status
     * @param CarbonImmutable $cutoff
     * @return LetterCollection
     */
    public function getLettersByStatusAndPublishDateCutoff(int $status, CarbonImmutable $cutoff): LetterCollection
    {
        $dateTimeByWhichToCutOffLetterQuery = $cutoff->toDateTimeString();
        try {
            $letters = app('db')->table(self::TABLE_LETTERS)
                ->where('status', '=', $status)
                ->where('publicationDate', '<=', $dateTimeByWhichToCutOffLetterQuery)
                ->get();

            return new LetterCollection($letters);
        } catch (\Exception $e) {
            return new LetterCollection();
        }
    }

    /**
     * Get a specific Letter by its ID. We'll first see if we have it in cache, and if not
     * try to pull it out of the database.
     *
     * @param int $letterId
     * @param bool $includeAuthors
     * @param bool $includeEmails
     * @param bool $includeParts
     * @return LetterDto|null
     */
    public function getLetterById(
        int $letterId
    ): ?LetterDto {
        /*
        $letterCacheKey = $this->getLetterCacheKeyByLetterId($letterId);

        $letterFromCache = $this->cache->get($letterCacheKey);

        if (empty($letterFromCache))
        {*/
            return $this->getLetterFromDatabaseById($letterId);
        // }

       // return $letterFromCache;
    }

    private function getLetterFromDatabaseById(int $letterId): ?LetterDto
    {
        try {
            $letterRow = app('db')
                ->table(self::TABLE_LETTERS)
                ->where('id', '=', $letterId)
                ->first();

            if (empty($letterRow)) {
                return null;
            }

            $authors = $this->getAuthorsByLetterId($letterId);
            $emails = $this->getEmailsByLetterId($letterId);
            $parts = $this->getLetterPartsByLetterId($letterId);

            $dto = new LetterDto($letterRow, null, $parts, $emails, $authors);
            $this->cacheLetterDto($dto, $this->getLetterCacheKeyByLetterId($letterId));

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function getLetterPartsByLetterId(int $letterId): LetterPartCollection
    {
        try {
            $letterPartRows = app('db')->table(self::TABLE_LETTER_PARTS)
                ->where('letterId', '=', $letterId)
                ->get();

            return new LetterPartCollection($letterPartRows);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new LetterPartCollection([]);
        }
    }

    public function getLettersByChannelId(int $channelId): LetterCollection
    {
        /*
        $lettersByChannelCacheKey = $this->getLettersCacheKeyByChannel($channelId);

        $collectionFromCache = $this->cache->get($lettersByChannelCacheKey);

        if (!empty($collectionFromCache))
        {
            return $collectionFromCache;
        }*/

        return $this->getLettersByChannelIdFromDatabase($channelId);
    }

    private function getLettersByChannelIdFromDatabase(int $channelId): LetterCollection
    {
        try {
            $letters = app('db')
                ->table(self::TABLE_LETTERS)
                ->where('channelId', '=', $channelId)
                ->whereNull('deleted_at')
                ->get();

            $collection = new LetterCollection($letters);

            if (!empty($collection->all())) {
                $this->cacheLettersByChannel($collection, $this->getLettersCacheKeyByChannel($channelId));
            }

            return $collection;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new LetterCollection([]);
        }
    }

    public function updateLetter(
        int $letterId,
        array $arrayOfAuthorIds,
        array $arrayOfParts,
        LetterDto $letterToUpdate
    ): ?LetterDto {

        try {
            app('db')->beginTransaction();

            $tblLetter = app('db')->table(self::TABLE_LETTERS);
            $tblUser   = app('db')->table(self::TABLE_LETTERS_USERS);
            $tblPart   = app('db')->table(self::TABLE_LETTER_PARTS);

            $columns = $letterToUpdate->mapChannelDtoToDatabaseColumns();

            unset($columns['id']);
            unset($columns['uniqueId']);
            unset($columns['channelId']);

            $tblLetter->where('id', '=', $letterId)
                      ->update($columns);

            // for simplicity we're just going to delete the users & parts and then re-insert rather than
            // trying to figure out which ones already exist.
            //

            $tblUser->where('letterId', '=', $letterId)->delete();
            $tblPart->where('letterId', '=', $letterId)->delete();

            $timeStampForCreation = CarbonImmutable::now()->toDateTimeString();

            $lettersUsersCollection = $this->generateLettersUsersCollection($arrayOfAuthorIds, $letterId);
            $letterPartCollection = $this->generateLetterPartsCollectionFromNewLetterAndArrayOfParts($arrayOfParts, $timeStampForCreation, $letterId);

            $tblUser->insert($lettersUsersCollection->getLettersUsersMappedToDatabaseColumns());
            $tblPart->insert($letterPartCollection->getLetterPartsMappedToDatabaseColumns());

            app('db')->commit();

            $this->forgetLetterCache($letterId);
            $this->forgetChannelLettersCache($letterToUpdate->channelId);

            /**
             * We'll then return the updated Letter. Specifically, we'll use this method that
             * retrieves it from the database directly and then re-caches it.
             */
            return $this->getLetterFromDatabaseById($letterId);
        } catch (\Exception $e) {
            app('db')->rollBack();
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
