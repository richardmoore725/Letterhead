<?php

namespace App\Http\Services;

use App\Http\Repositories\BeaconRepositoryInterface;

class BeaconService implements BeaconServiceInterface
{
    private $repository;

    public function __construct(BeaconRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createResourceByBeaconSlug(
        string $beaconSlug,
        int $brandId,
        int $channelId,
        string $restfulResourcePath,
        $signal,
        bool $signalIsMultipart
    ) {
        $endpoint = $this->getServiceEndpoint($beaconSlug);
        $key = $this->getServiceKey($beaconSlug);
        $api = "{$endpoint}/{$restfulResourcePath}";

        return $this->repository->createBrandChannelResourceFromService($api, $key, $brandId, $channelId, $signal, $signalIsMultipart);
    }

    public function deleteResourceFromService(string $beaconSlug, string $restfulResourcePath)
    {
        $endpoint = $this->getServiceEndpoint($beaconSlug);
        $key = $this->getServiceKey($beaconSlug);
        $api = "{$endpoint}/{$restfulResourcePath}";

        return $this->repository->deleteResourceFromService($api, $key);
    }

    public function getResourceByBeaconSlug(
        string $beaconSlug,
        int $brandId,
        int $channelId,
        string $restfulResourcePath
    ) {
        $endpoint = $this->getServiceEndpoint($beaconSlug);
        $key = $this->getServiceKey($beaconSlug);
        $api = "{$endpoint}/{$restfulResourcePath}";

        return $this->repository->getBrandChannelResourceFromService($api, $key, $brandId, $channelId, $restfulResourcePath);
    }

    public function getAdResourceByBeaconSlug(
        string $beaconSlug,
        string $restfulResourcePath
    ) {
        $endpoint = $this->getServiceEndpoint($beaconSlug);
        $key = $this->getServiceKey($beaconSlug);
        $api = "{$endpoint}/{$restfulResourcePath}";

        return $this->repository->getAdResourceFromService($api, $key, $restfulResourcePath);
    }

    private function getServiceEndpoint(string $beaconSlug): string
    {
        $serviceEndpointEnvironmentVariable = strtoupper("SERVICE_{$beaconSlug}_ENDPOINT");
        return env($serviceEndpointEnvironmentVariable);
    }

    private function getServiceKey(string $beaconSlug): string
    {
        $serviceKeyEnvironmentVariable = strtoupper("SERVICE_{$beaconSlug}_KEY");
        return env($serviceKeyEnvironmentVariable);
    }
}
