<?php

namespace App\Http\Repositories;

use App\DTOs\PassportStampDto;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Http\Request;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

/**
 * Class AuthRepository
 * @package App\Http\Repositories
 */
class AuthRepository implements AuthRepositoryInterface
{
    private $cache;
    private $client;

    public function __construct(Cache $cache, ClientInterface $client)
    {
        $this->cache = $cache;
        $this->client = $client;
    }

    /**
     * @param string $passportToken
     * @return bool
     * @see http://docs.guzzlephp.org/en/stable/quickstart.html#creating-a-client
     */
    public function authenticatePassport(string $origin, string $passportToken): ?PassportStampDto
    {
        try {
            $userServiceUrl = env('SERVICE_PASSPORT_URL');
            $response = $this->client->get("{$userServiceUrl}/api/v1/authorize", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$passportToken}",
                    'Origin' => $origin,
                ],
                'verify' => env('APP_DEBUG') ? false : true,
            ]);

            $responseBody = $response->getBody();
            $contents = $responseBody->getContents();

            $passportStamp = json_decode($contents);
            $passportStampDto = new PassportStampDto($passportStamp, $passportToken);

            return $passportStampDto;
        } catch (ClientException $e) {
            /**
             * A ClientException is thrown by Guzzle when there is a 400-level error, meaning that
             * UserService either cannot find or has deliberately rejected a request.
             */
            return null;
        } catch (ServerException $e) {
            /**
             * A server exception is thrown for 500-level errors
             */
            Rollbar::log(Level::ERROR, 'A ServerException was thrown connecting to UserSrvice', ['exception' => $e->getMessage()]);
            return null;
        } catch (RequestException $e) {
            /**
             * A RequestException is thrown in the event of any other error with the request.
             */
            Rollbar::log(Level::ERROR, 'A RequestException was thrown connecting to UserService', ['exception' => $e->getMessage()]);
            return null;
        } catch (\Exception $e) {
            $errorMessage = "There was an error connecting to UserService: {$e->getMessage()}";
            Rollbar::log(Level::ERROR, $errorMessage, ['exception' => $e]);
            return null;
        }
    }
}
