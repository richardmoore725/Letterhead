<?php

namespace App\Http\Repositories;

use App\Http\Response;

interface BeaconRepositoryInterface
{
    public function createBrandChannelResourceFromService(
        string $api,
        string $bearerToken,
        string $brandId,
        string $channelId,
        $signal,
        bool $signalIsMultipart
    );

    public function deleteResourceFromService(string $api, string $bearerToken);

    public function getBrandChannelResourceFromService(
        string $api,
        string $bearerToken,
        string $brandId,
        string $channelId,
        string $restfulResourcePath
    );

    public function getAdResourceFromService(
        string $api,
        string $bearerToken,
        string $endpoint
    );

    public function getResourceFromService(string $api, string $bearerToken);

    public function getResponseFromApi(
        string $api,
        string $bearerToken,
        string $method,
        array $requestBody,
        string $authorizationType = 'Bearer',
        bool $requestIsMultipartForm = false
    ): Response;

    public function getResourceFromServiceWithRequestData(
        string $api,
        string $bearerToken,
        $signal,
        bool $signalIsMultipart
    );
}
