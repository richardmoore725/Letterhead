<?php

namespace App\Http\Services;

use App\Collections\LetterCollection;
use App\Collections\UserCollection;
use App\Formatters\LetterDeltaMJMLFormatter;
use App\Formatters\LetterDeltaMJMLFormatterInterface;
use App\Formatters\LetterDeltaParserSegmentSectionFormatter;
use App\Http\Repositories\EspRepositoryInterface;
use App\Http\Repositories\LetterRepositoryInterface;
use App\Http\Repositories\MailChimpRepositoryInterface;
use App\Http\Repositories\ConstantContactRepositoryInterface;
use App\Http\Repositories\MjmlTemplateRepositoryInterface;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\LetterPart;
use App\Models\Promotion;
use Carbon\CarbonImmutable;
use nadar\quill\Lexer;
use nadar\quill\listener\Image;
use RandomLib\Factory;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Symfony\Component\DomCrawler\Crawler;

class LetterService implements LetterServiceInterface
{
    private $formatter;
    private $repository;
    private $mailchimpRepository;
    private $constantContactRepository;
    private $mjmlTemplateRepository;

    public function __construct(
        LetterDeltaMJMLFormatterInterface $formatter,
        LetterRepositoryInterface $repository,
        MailChimpRepositoryInterface $mailChimpRepository,
        ConstantContactRepositoryInterface $constantContactRepository,
        MjmlTemplateRepositoryInterface $mjmlTemplateRepository
    ) {
        $this->formatter = $formatter;
        $this->repository = $repository;
        $this->mailchimpRepository = $mailChimpRepository;
        $this->constantContactRepository = $constantContactRepository;
        $this->mjmlTemplateRepository = $mjmlTemplateRepository;
    }

    public function createLetter(
        array $arrayOfAuthorIds,
        array $arrayOfEmptyLetterParts,
        Letter $letterToCreate
    ): ?Letter {
        $creationTime = CarbonImmutable::now()->toDateTimeString();
        $letterToCreate->setCreatedAt($creationTime);
        $letterToCreate->setUpdatedAt($creationTime);
        $letterToCreate->setUniqueId($this->generateUniqueIdentifier());

        $letterDto = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $letterToCreate->convertToDto(), $creationTime);

        return empty($letterDto) ? null : new Letter($letterDto);
    }


    /**
     * We call `generateEmptyLetter` via middleware every time we create and update a Letter. This will
     * generate a new Letter object - which is probably a better name for this method - that we can use
     * to compare with an existing Letter object, etc.
     *
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
    ): Letter {
        $publicationDateAsString = $publicationDate === "" ? $publicationDate : CarbonImmutable::parse($publicationDate)->toDateTimeString();
        $creationTime = CarbonImmutable::now()->toDateTimeString();
        $letter = new Letter();

        $letter->setCampaignId($campaignId);
        $letter->setChannelId($channel->getId());

        /**
         * Right now, Letters - via Authoring - are only accessible to those authorized with MailChimp.
         * As such, we will hardcode "MailChimp" as the letter's email service provider.
         */
        $letter->setEmailServiceProvider(Letter::LETTER_ESP_MAILCHIMP);

        /**
         * Similarly, the List Id at the moment of this writing is set as a Configuration option on
         * the Channel, rather than something passed to the Letter itself. We will just set this
         * value as whatever the Channel has.
         */
        $letter->setEmailServiceProviderListId($channel->getDefaultListId());

        $letter->setPublicationDate($publicationDateAsString);
        $letter->setPublicationDateOffset($channel->getTimezoneOffset());
        $letter->setPublicationStatus($publicationStatus);
        $letter->setIncludePromotions($includePromotions);
        $letter->setMjmlTemplate($mjmlTemplate);
        $letter->setSegmentId($segmentId);
        $letter->setSlug($slug);
        $letter->setSubtitle($subtitle);
        $letter->setSpecialBanner($specialBanner);
        $letter->setTitle($title);
        $letter->setCreatedAt($creationTime);
        $letter->setUpdatedAt($creationTime);

        return $letter;
    }

    /**
     * @param array $arrayOfAuthors
     * @return string
     * @deprecated
     */
    private function generateLetterBylineFromArrayOfAuthors(array $arrayOfAuthors): string
    {
        if (empty($arrayOfAuthors)) {
            return 'Unknown author';
        }

        if (sizeof($arrayOfAuthors) === 1) {
            return implode('|', $arrayOfAuthors);
        }

        if (sizeof($arrayOfAuthors) === 2) {
            return implode(' and ', $arrayOfAuthors);
        }

        $arrayOfNamesJoinWithComas = array_slice($arrayOfAuthors, 0, sizeof($arrayOfAuthors) - 1);
        $stringOfNamesJoinWithComas = implode(', ', $arrayOfNamesJoinWithComas);

        return $stringOfNamesJoinWithComas . ' and ' . array_pop($arrayOfAuthors);
    }

    /**
     * @param UserCollection $authors
     * @param Channel $channel
     * @param bool $includePixel
     * @param Letter $letter
     * @return Response
     * @deprecated
     */
    public function generateLetterMarkup(UserCollection $authors, Channel $channel, bool $includePixel, Letter $letter): Response
    {
        /**
         * @see resources/views/newsletter.blade.php
         */
        $nameOfBladeTemplate = 'newsletter';

        $arrayOfAuthorFullNames = $authors->getArrayOfUserFullNames();
        $byline = $this->generateLetterBylineFromArrayOfAuthors($arrayOfAuthorFullNames);

        $letterBanner = empty($letter->getSpecialBanner())
                        ? $channel->getChannelHorizontalLogo()
                        : $letter->getSpecialBanner();

        $arrayOfTemplateVariables = [
            'authors' => $byline,
            'channel' => $channel,
            'insertPixel' => $includePixel,
            'newsletter' => $letter,
            'pixel' => $this->getLetterTrackingPixel($letter),
            'banner' => $letterBanner
        ];

        try {
            $markup = view($nameOfBladeTemplate, $arrayOfTemplateVariables)->render();
            return new Response($markup);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Rollbar::log(Level::ERROR, $errorMessage);
            return new Response($errorMessage, 500);
        }
    }

    public function generateLetterEmailTemplate(UserCollection $authors, Channel $channel, string $delta, Letter $letter, array $promotions): Response
    {
        $template = $this->formatter->renderMjmlTemplate($authors, $channel, $delta, $letter, $promotions);

        if ($template->isError()) {
            return $template;
        }

        $repositoryResponse = $this->mjmlTemplateRepository->getHtmlFromMjml($template->getData());

        if ($repositoryResponse->isError()) {
            return $repositoryResponse;
        }

        $responseBody = $repositoryResponse->getData();
        $htmlTemplate = isset($responseBody->dom) ? $responseBody->dom : '';

        if (empty($htmlTemplate)) {
            return new Response('Our MJML rendering engine failed to return anything', 500);
        }

        $letter->setEmailTemplate($htmlTemplate);
        $wasLetterUpdatedWithTemplate = $this->updateLetter($letter->getId(), $letter->getAuthors()->getPublicArray(), $letter->getParts()->getArrayOfModels(), $letter);

        if (empty($wasLetterUpdatedWithTemplate)) {
            return new Response('We couldn\'t update your content with the generated email template', 500);
        }

        return new Response('Your template, stranger.', 200, $htmlTemplate);
    }

    /**
     * Generate an empty LetterPart we can pass around the app as needed, likely
     * in the period before it is actually created in the database.
     *
     * @param string $copy
     * @param string $heading
     * @return LetterPart
     */
    public function generateEmptyLetterPart(string $copy, string $heading, int $id = null): LetterPart
    {
        $part = new LetterPart();
        $part->setCopy($copy);
        $part->setHeading($heading);

        if (!empty($id)) {
            $part->setId($id);
        }

        return $part;
    }

    /**
     * Generate a 10 character long random string to serve as the
     * Letter's uniqueId.
     *
     * @return string
     */
    private function generateUniqueIdentifier(): string
    {
        $charactersToComposeKey = 'abcdefghiklmnopqrstuvwxyz0123456789';
        $randomStringFactory = new Factory();
        $randomStringGenerator = $randomStringFactory->getMediumStrengthGenerator();

        return $randomStringGenerator->generateString(10, $charactersToComposeKey);
    }

    /**
     * Get the appropriate access token for the specific esp.
     *
     * @param Channel $channel
     * @param int $esp
     * @return string
     */
    private function getEspAccessTokenByEsp(Channel $channel, int $esp): string
    {
        $accessTokensMappedToEsps = [
            0 => '',
            1 => $channel->getChannelConfigurations()->getMcApiKey(),
            2 => $this->constantContactRepository->getAccessTokenFromChannel($channel),
        ];

        return $accessTokensMappedToEsps[$esp];
    }

    /**
     * The Esp repository must implement the EspRepositiryInterface if it's to be
     * included here. This is a simple helper for returning the "right" repository
     * when there are multiple options.
     *
     * @param int $esp
     * @return EspRepositoryInterface
     */
    private function getServiceRepositoryByEsp(int $esp): EspRepositoryInterface
    {
        $espIntegerMappedToRepositoryClasses = [
            1 => $this->mailchimpRepository,
            2 => $this->constantContactRepository,
        ];

        return $espIntegerMappedToRepositoryClasses[$esp];
    }

    /**
     * Deleting a letter will also trash all corresponding LetterParts
     *
     * @param Letter $letter
     * @return bool
     */
    public function deleteLetter(Letter $letter): bool
    {
        return $this->repository->deleteLetter($letter->convertToDto());
    }

    /**
     * update letter status to published
     *
     * @param Letter $letter
     * @return bool
     */
    public function markLetterAsPublished(Letter $letter): bool
    {
        return $this->repository->setLetterStatus($letter->getId(), Letter::PUBLICATION_STATUS_PUBLISHED);
    }

    /**
     * Get a letter by its ID.
     * @param int $letterId
     * @return Letter|null
     */
    public function getLetterById(int $letterId): ?Letter
    {
        $dto = $this->repository->getLetterById($letterId);

        if (empty($dto)) {
            return null;
        }

        return new Letter($dto);
    }

    public function getLettersByChannelId(int $channelId): LetterCollection
    {
        return $this->repository->getLettersByChannelId($channelId);
    }

    /**
     * A tracking pixel is just an 1x1 pixel generated on a pixelService in our
     * ecosystem, that as it renders - when someone visits the url or a browser
     * tries to render the image - tracks it.
     *
     * A letter pixel is just special url unique to it, made unique by its uniqueId.
     * If no unique Id is present, we'll return an empty string.
     * @param Letter $letter
     * @return string
     */
    public function getLetterTrackingPixel(Letter $letter): string
    {
        $uniqueIdentifier = $letter->getUniqueId();

        if (empty($uniqueIdentifier)) {
            return '';
        }

        $pixelServiceUrl = env('SERVICE_PIXEL_URL', '');
        $pixelUrl = "{$pixelServiceUrl}/{$uniqueIdentifier}/*|UNIQID|*/l.jpg";

        return $pixelUrl;
    }

    /**
     * Get all letters that are ready to be published.
     * @return LetterCollection
     */
    public function getPublishableLetters(): LetterCollection
    {
        return $this->repository->getLettersByStatusAndPublishDateCutoff(Letter::PUBLICATION_STATUS_SCHEDULED, CarbonImmutable::now());
    }

    public function send(
        Channel $channel,
        int $esp,
        Letter $letter,
        string $returnEmailAddress,
        string $senderName,
        string $subject,
        string $template
    ): Response {
        $accessToken = $this->getEspAccessTokenByEsp($channel, $esp);

        if (empty($accessToken)) {
            return new Response('It doesn\'t appear this channel has the right access token', 400);
        }

        $repository = $this->getServiceRepositoryByEsp($esp);

        return $repository->send($accessToken, $letter, $returnEmailAddress, $senderName, $subject, $template);
    }

    public function test(
        Channel $channel,
        string $emailAddress,
        int $esp,
        Letter $letter,
        string $returnEmailAddress,
        string $senderName,
        string $subject,
        string $template
    ): Response {
        $accessToken = $this->getEspAccessTokenByEsp($channel, $esp);

        if (empty($accessToken)) {
            return new Response('It doesn\'t appear this channel has the right access token', 400);
        }

        $repository = $this->getServiceRepositoryByEsp($esp);

        return $repository->test($accessToken, $emailAddress, $letter, $returnEmailAddress, $senderName, $subject, $template);
    }

    public function updateLetter(int $id, array $arrayOfAuthorIds, array $letterParts, Letter $letter): ?Letter
    {
        $letterDto = $this->repository->updateLetter($id, $arrayOfAuthorIds, $letterParts, $letter->convertToDto());
        return empty($letterDto) ? null : new Letter($letterDto);
    }
}
