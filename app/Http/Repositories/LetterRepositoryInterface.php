<?php

namespace App\Http\Repositories;

use App\Collections\LetterCollection;
use App\DTOs\LetterDto;
use Carbon\CarbonImmutable;

interface LetterRepositoryInterface
{
    public function getLetterById(int $letterId): ?LetterDto;
    public function getLettersByChannelId(int $channelId): LetterCollection;
    public function getLettersByStatusAndPublishDateCutoff(int $status, CarbonImmutable $cutoff): LetterCollection;

    public function createLetter(array $arrayOfAuthorIds, array $arrayOfEmptyLetterParts, LetterDto $letterToCreate, string $timeStampForCreation): ?LetterDto;
    public function updateLetter(int $letterId, array $arrayOfAuthorIds, array $arrayOfParts, LetterDto $letterToUpdate): ?LetterDto;
    public function deleteLetter(LetterDto $dto): bool;
    public function setLetterStatus(int $letterId, int $status): bool;
}
