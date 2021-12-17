<?php

namespace App\Http\Services;

interface BeaconServiceInterface
{
    public function createResourceByBeaconSlug(
        string $beaconSlug,
        int $brandId,
        int $channelId,
        string $restfulResourcePath,
        $signal,
        bool $signalIsMultipart
    );

    public function deleteResourceFromService(string $beaconSlug, string $restfulResourcePath);

    public function getResourceByBeaconSlug(
        string $beaconSlug,
        int $brandId,
        int $channelId,
        string $restfulResourcePath
    );

    public function getAdResourceByBeaconSlug(
        string $beaconSlug,
        string $restfulResourcePath
    );
}
