<?php

namespace App\Http\Services;

use App\Collections\LetterCollection;
use App\Collections\UserCollection;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\LetterPart;

interface LetterServiceInterface
{
    /**
     * Create a new letter from an array of author Ids, LetterParts, and the Letter object to create.
     * @param array $arrayOfAuthorIds
     * @param array $arrayOfEmptyLetterParts
     * @param Letter $letterToCreate
     * @return Letter|null
     */
    public function createLetter(array $arrayOfAuthorIds, array $arrayOfEmptyLetterParts, Letter $letterToCreate): ?Letter;

    /**
     * Deleting a letter will also trash all corresponding LetterParts
     *
     * @param Letter $letter
     * @return bool
     */
    public function deleteLetter(Letter $letter): bool;

    /**
     * Generate an empty Letter from the properties passed.
     * @param string $campaignId
     * @param Channel $channel
     * @param string $publicationDate
     * @param int $publicationStatus
     * @param bool $includePromotions
     * @param string $mjmlTemplate
     * @param int $segmentId
     * @param string $slug
     * @param string $subtitle
     * @param string $specialBanner
     * @param string $title
     * @return Letter
     */
    public function generateEmptyLetter(
        string $campaignId,
        Channel $channel,
        string $publicationDate,
        int $publicationStatus,
        bool $includePromotions,
        string $mjmlTemplate,
        int $segmentId,
        string $slug,
        string $subtitle,
        string $specialBanner,
        string $title
    ): Letter;

    /**
     * Generate an empty LetterPart from the properties passed.
     * @param string $copy
     * @param string $heading
     * @return LetterPart
     */
    public function generateEmptyLetterPart(string $copy, string $heading, int $id = null): LetterPart;

    public function generateLetterMarkup(UserCollection $authors, Channel $channel, bool $includePixel, Letter $letter): Response;

    /**
     * Get an HTML email template for the Letter.
     *
     * @param UserCollection $authors
     * @param Channel $channel
     * @param Letter $letter
     * @return Response
     */
    public function generateLetterEmailTemplate(UserCollection $authors, Channel $channel, string $delta, Letter $letter, array $promotions): Response;

    /**
     * Get a Letter by its ID.
     *
     * @param int $letterId
     * @return Letter|null
     */
    public function getLetterById(int $letterId): ?Letter;

    public function getLetterTrackingPixel(Letter $letter): string;

    public function getLettersByChannelId(int $channelId): LetterCollection;

    /**
     * Get all letters that are ready to be published.
     * @return Array
     */
    public function getPublishableLetters(): LetterCollection;

    public function markLetterAsPublished(Letter $letter): bool;

    public function send(
        Channel $channel,
        int $esp,
        Letter $letter,
        string $returnEmailAddress,
        string $senderName,
        string $subject,
        string $template
    ): Response;

    public function test(
        Channel $channel,
        string $emailAddress,
        int $esp,
        Letter $letter,
        string $returnEmailAddress,
        string $senderName,
        string $subject,
        string $template
    ): Response;

    /**
     * Updates a letter with new letter parts and new authors
     *
     * @param int $letterId
     * @param array $arrayOfAuthorIds
     * @param array $arrayOfEmptyLetterParts
     * @param Letter $letterToCreate
     * @return Letter|null
     *
     */
    public function updateLetter(int $letterId, array $arrayOfAuthorIds, array $letterParts, Letter $letter): ?Letter;
}
