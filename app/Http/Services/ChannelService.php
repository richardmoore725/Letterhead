<?php

namespace App\Http\Services;

use App\DTOs\ChannelDto;
use App\Http\Repositories\ChannelRepositoryInterface;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Configuration;
use App\Models\ChannelConfiguration;
use App\Http\Repositories\ConfigurationRepositoryInterface;
use App\Collections\ChannelCollection;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Http\UploadedFile;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class ChannelService implements ChannelServiceInterface
{
    private $repository;
    private $configurationRepository;

    public function __construct(
        ChannelRepositoryInterface $repository,
        ConfigurationRepositoryInterface $configurationRepository
    ) {
        $this->repository = $repository;
        $this->configurationRepository = $configurationRepository;
    }

    public function createChannel(
        string $accentColor,
        int $brandId,
        string $channelDescription,
        string $channelHorizontalLogo,
        string $channelImage,
        string $channelSlug,
        string $channelSquareLogo,
        string $defaultEmailFromName,
        string $defaultFromEmailAddress,
        string $defaultFont,
        bool $enableChannelAuthoring,
        string $headingFont,
        bool $loadPromosBeforeHeadings,
        string $title
    ): ?Channel {

        $channel = new Channel();
        $channel->setAccentColor($accentColor);
        $channel->setBrandId($brandId);
        $channel->setChannelSlug($channelSlug);
        $channel->setChannelDescription($channelDescription);
        $channel->setChannelImage($channelImage);
        $channel->setTitle($title);
        $channel->setChannelHorizontalLogo($channelHorizontalLogo);
        $channel->setChannelSquareLogo($channelSquareLogo);
        $channel->setDefaultEmailFromName($defaultEmailFromName);
        $channel->setDefaultFromEmailAddress($defaultFromEmailAddress);
        $channel->setDefaultFont($defaultFont);
        $channel->setEnableChannelAuthoring($enableChannelAuthoring);
        $channel->setHasValidMailChimpKey(false);
        $channel->setHeadingFont($headingFont);
        $channel->setLoadPromosBeforeHeadings($loadPromosBeforeHeadings);
        $channel->setTimeSinceMailChimpStatusPinged('');

        $newlyCreatedChannelDto = $this->repository->createChannel($channel->convertToDto());

        if (empty($newlyCreatedChannelDto)) {
            return null;
        }

        return new Channel($newlyCreatedChannelDto);
    }

    public function deleteChannel(Channel $channel): bool
    {
        return $this->repository->deleteChannel($channel->convertToDto());
    }

    /**
     * Find a channel with a specific brand API key.
     *
     * @param string $key
     * @return Response
     */
    public function getChannelByBrandApiKey(string $key): Response
    {
        $repositoryResponse = $this->repository->getChannelByBrandApiKey($key);

        if ($repositoryResponse->isError()) {
            return $repositoryResponse;
        }

        /**
         * If the response is successful, ChannelRepository returns a dto.
         * @var ChannelDto
         */
        $dto = $repositoryResponse->getData();
        $channel = new Channel($dto);

        return new Response('', 200, $channel);
    }

    public function getChannelById(int $channelId): ?Channel
    {
        $dto = $this->repository->getChannelById($channelId);

        if (empty($dto)) {
            return null;
        }

        return new Channel($dto);
    }

    public function getChannelBySlug(string $slug): ?Channel
    {
        $dto = $this->repository->getChannelBySlug($slug);

        if (empty($dto)) {
            return null;
        }

        return new Channel($dto);
    }

    /**
     * Given either a string or UploadedFile, such as from a request key, we'll return
     * either upload the image and return the path or return an empty string.
     *
     * @param $channel Channel
     * @param \Illuminate\Http\UploadedFile|string $imageObject
     * @return string
     */
    public function getChannelImagePath(Channel $channel, $imageObject): string
    {
        if (empty($imageObject) || $imageObject === 'null') {
            return '';
        }

        return is_a($imageObject, UploadedFile::class)
            ? $this->uploadChannelMedia($channel, $imageObject)
            : $imageObject;
    }

    public function updateChannel(Channel $channel): ?Channel
    {
        $updatedDto = $this->repository->updateChannel($channel->convertToDto());
        return empty($updatedDto) ? null : new Channel($updatedDto);
    }

    /**
     * Given an UploadedFile, we will generate a new file name and store it publicly - meaning
     * it is accessible from a URL - on DigitalOcean Spaces.
     *
     * @param Channel $channel
     * @param UploadedFile $file
     * @return string
     */
    private function uploadChannelMedia(Channel $channel, UploadedFile $file): string
    {
        $currentTime = time();
        $channelSpacesPath = "platformservice/brands/{$channel->getBrandId()}/channels/{$channel->getId()}";
        $mediaLocation = 'spaces';
        $newFileName = "{$channel->getSlug()}-{$currentTime}-image.{$file->extension()}";

        return $file->storePubliclyAs($channelSpacesPath, $newFileName, $mediaLocation);
    }

    public function getChannels(): ChannelCollection
    {
        return $this->repository->getChannels();
    }

    public function getChannelsThatAutoSyncListStats(): ChannelCollection
    {
        return $this->repository->getChannelsThatAutoSyncListStats();
    }
}
