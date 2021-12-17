<?php

namespace App\Http\Repositories;

use App\DTOs\ChannelConfigurationDto;
use App\DTOs\ChannelDto;
use App\Collections\ChannelCollection;
use App\Http\Response;

interface ChannelRepositoryInterface
{
    public function createChannel(ChannelDto $dto): ?ChannelDto;
    public function createChannelConfiguration(ChannelConfigurationDto $dto): ?int;
    public function deleteChannel(ChannelDto $dto): bool;
    public function getChannelByBrandApiKey(string $key): Response;
    public function getChannelById(int $channelId): ?ChannelDto;
    public function getChannelBySlug(string $slug): ?ChannelDto;
    public function getChannelsByBrandId(int $brandId): array;
    public function getChannels(): ChannelCollection;
    public function updateChannel(ChannelDto $dto): ?ChannelDto;
    public function updateChannelConfiguration(int $channelId, $channelConfigurationValue, int $configurationId): bool;
    public function getChannelsThatAutoSyncListStats(): ChannelCollection;
}
