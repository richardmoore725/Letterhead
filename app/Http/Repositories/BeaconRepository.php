<?php

namespace App\Http\Repositories;

use App\Http\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class BeaconRepository implements BeaconRepositoryInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Request
     */
    private $request;

    public function __construct(ClientInterface $client, Request $request)
    {
        $this->client = $client;
        $this->request = $request;
    }

    public function createBrandChannelResourceFromService(
        string $api,
        string $bearerToken,
        string $brandId,
        string $channelId,
        $signal,
        bool $signalIsMultipart
    ) {
        $postingMethod = $signalIsMultipart ? 'multipart' : 'json';

        try {
            $response = $this->client->post($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'verify' => env('APP_DEBUG') ? false : true,
                "{$postingMethod}" => $signal,
            ]);

            $responseBody = $response->getBody();

            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (RequestException $e) {
            Rollbar::log(Level::WARNING, $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * Use this method to send a `delete` request over the wire to one of our
     * second-party services.
     *
     * @param string $api
     * @param string $bearerToken
     * @return mixed|null
     */
    public function deleteResourceFromService(
        string $api,
        string $bearerToken
    ) {
        try {
            $response = $this->client->delete($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (RequestException $e) {
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * @deprecated
     *
     * @param string $api
     * @param string $bearerToken
     * @param string $brandId
     * @param string $channelId
     * @param string $endpoint
     * @return mixed|null
     */
    public function getBrandChannelResourceFromService(
        string $api,
        string $bearerToken,
        string $brandId,
        string $channelId,
        string $endpoint
    ) {
        try {
            $response = $this->client->get($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (RequestException $e) {
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * @deprecated
     *
     * @param string $api
     * @param string $bearerToken
     * @param string $endpoint
     * @return mixed|null
     */
    public function getAdResourceFromService(
        string $api,
        string $bearerToken,
        string $endpoint
    ) {
        try {
            $response = $this->client->get($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (RequestException $e) {
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * @param string $api
     * @param string $bearerToken
     * @return mixed|null
     */
    public function getResourceFromService(string $api, string $bearerToken)
    {
        try {
            $origin = $this->request->headers->get('origin');

            $response = $this->client->get($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                    'Origin' => $origin,
                ],
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (ClientException $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        } catch (ConnectException $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        } catch (ServerException $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getResourceFromServiceWithRequestData(
        string $api,
        string $bearerToken,
        $signal,
        bool $signalIsMultipart
    ) {
        $postingMethod = $signalIsMultipart ? 'multipart' : 'json';

        try {
            $response = $this->client->get($api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$bearerToken}",
                ],
                'verify' => env('APP_DEBUG') ? false : true,
                "{$postingMethod}" => $signal,
            ]);

            $responseBody = $response->getBody();

            $responseContents = json_decode($responseBody->getContents());

            return $responseContents;
        } catch (RequestException $e) {
            return null;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * Make a Guzzle POST or GET request to an external API and return a Response object.
     * @param string $api
     * @param string $bearerToken
     * @param string $method
     * @param array $requestBody
     * @param string $authorizationType
     * @param bool $requestIsMultipartForm
     * @return Response
     */
    public function getResponseFromApi(
        string $api,
        string $bearerToken,
        string $method,
        array $requestBody,
        string $authorizationType = 'Bearer',
        bool $requestIsMultipartForm = false
    ): Response {
        $requestFormat = $requestIsMultipartForm ? 'multipart' : 'json';

        try {
            $origin = $this->request->headers->get('origin');

            /**
             * @var \GuzzleHttp\Psr7\Response
             */
            $response = $this->client->request($method, $api, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "{$authorizationType} {$bearerToken}",
                    'Origin' => $origin,
                ],

                /**
                 * Setting `http_errors` to false means that we won't throw an exception if
                 * the request fails because of a 400 error. We're doing this because we trust
                 * the API [because we probably wrote it] to return a meaningful response.
                 */
                'http_errors' => false,
                "{$requestFormat}" => $requestBody,
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $responseContents = json_decode($responseBody->getContents());

            $statusCode = $response->getStatusCode();

            return new Response('', $statusCode, $responseContents);
        } catch (ClientException $e) {
            /**
             * A ClientException is thrown on a 400-level error.
             */
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        } catch (ConnectException $e) {
            /**
             * A ConnectException is thrown when there is a networking error.
             */
            return new Response($e->getMessage(), 500);
        } catch (ServerException $e) {
            /**
             * A ServerException is thrown for 500 level errors.
             */
            return new Response($e->getMessage(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }
}
