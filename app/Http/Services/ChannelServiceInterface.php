<?php

namespace App\Http\Services;

use App\Http\Response;
use App\Models\Channel;
use App\Models\Configuration;
use Illuminate\Http\UploadedFile;
use App\Collections\ChannelCollection;

interface ChannelServiceInterface
{
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
    ): ?Channel;
    public function getChannelByBrandApiKey(string $key): Response;
    public function deleteChannel(Channel $channel): bool;
    public function getChannelById(int $channelId): ?Channel;
    public function getChannelBySlug(string $slug): ?Channel;
    public function getChannels(): ChannelCollection;

    /**
     * Return either an empty string or a full url, depending on whether or not the
     * variable is an UploadedFile.
     *
     * @param $channel Channel
     * @param $imageObject string|UploadedFile
     * @return string
     */
    public function getChannelImagePath(Channel $channel, $imageObject): string;
    public function updateChannel(Channel $channel): ?Channel;
    public function getChannelsThatAutoSyncListStats(): ChannelCollection;
}
