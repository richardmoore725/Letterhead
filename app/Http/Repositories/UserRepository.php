<?php

namespace App\Http\Repositories;

use App\DTOs\UserDto;
use App\Http\Response;
use App\Models\PassportStamp;

/**
 * The UserRepository is responsible largely for interfacing with UserService, either
 * over the API or with one of its related resources. While we don't usually like to
 * extend classes, UserRepository extends BeaconRepository to utilize its API
 * connectors.
 *
 * Class UserRepository
 * @package App\Http\Repositories
 */
class UserRepository extends BeaconRepository implements UserRepositoryInterface
{
    /**
     * @param string $action
     * @param string $model
     * @param PassportStamp $passport
     * @param int $resourceId
     * @return Response
     */
    public function checkWhetherUserCanPerformAction(
        string $action,
        string $model,
        PassportStamp $passport,
        int $resourceId
    ): Response {
        $endpoint = "{$this->getUserEndpoint()}/{$passport->getId()}/permissions/check";
        $requestBody = [
            'action' => $action,
            'resource' => $model,
            'resourceId' => $resourceId,
        ];

        /**
         * @see BeaconRepository::getResponseFromApi()
         */
        return $this->getResponseFromApi($endpoint, $passport->getToken(), 'GET', $requestBody);
    }

    public function getOrCreateAndChargeUser(
        int $applicationFeeAmount,
        string $connectedStripeAccountId,
        string $description,
        int $finalPriceOfPackage,
        string $paymentMethod,
        string $userEmail,
        string $userName
    ): ?UserDto {
        $endpoint = "{$this->getUserEndpoint()}/charge";
        $passportToken = $this->getUserServiceKey();
        $requestBody = [
            'email' => $userEmail,
            'name' => $userName,
            'amount' => $finalPriceOfPackage,
            'paymentMethod' => $paymentMethod,
            'currency' => 'usd',
            'applicationFeeAmount' => $applicationFeeAmount,
            'connectedStripeAccountId' => $connectedStripeAccountId,
            'description' => "$description"
        ];

        $response = $this->getResponseFromApi($endpoint, $passportToken, 'POST', $requestBody);

        if ($response->isError()) {
            return null;
        }

        $data = $response->getData();

        $userDto = new UserDto($data, null);

        return $userDto;
    }

    private function getUserEndpoint(): string
    {
        $endpoint = env('SERVICE_USERS_ENDPOINT');

        return "{$endpoint}/users";
    }

    private function getUserServiceKey(): string
    {
        $key = env('SERVICE_USERS_KEY');

        return $key;
    }
}
