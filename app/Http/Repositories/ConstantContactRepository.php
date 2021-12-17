<?php

namespace App\Http\Repositories;

use App\Http\Response;
use RandomLib\Factory;
use App\Http\Services\ChannelServiceInterface;

class ConstantContactRepository extends BeaconRepository implements ConstantContactRepositoryInterface
{
    private $header;
    private $redirect_uri;

    public function getAccessToken($code): array
    {
        $data = [
            'code'          => $code,
            'redirect_uri'  => $this->getRedirectUri(),
            'grant_type'    => 'authorization_code'
        ];
        $res = $this->getResponseFromApi($this->getOAuthApiRoot() . '?' . http_build_query($data), $this->getOAuth(), 'POST', [], 'Basic');
        return (array)$res->getData();
    }

    public function getNewAccessToken($refreshToken)
    {
        $data = [
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token'
        ];
        $res = $this->getResponseFromApi($this->getOAuthApiRoot() . '?' . http_build_query($data), $this->getOAuth(), 'POST', [], 'Basic');
        return (array)$res->getData();
    }

    /**
     * @param Channel $channel
     * check if the access token in channel is valid and get new access token if expires
     */
    public function getAccessTokenFromChannel($channel): string
    {
        if ($channel->isAccessTokenExpired()) {
            $token = $this->getNewAccessToken($channel->getRefreshToken());
            if (isset($token['error'])) {
                $channel->clearCCTokens();
                return '';
            }
            $channel->setCCAccessToken($token['access_token']);
            $channel->setCCRefreshToken($token['refresh_token']);
            $channel->setCCAccessTokenLastUsed();
            $channelService = new ChannelServiceInterface();
            $updateChannel = $channelService->updateChannel($channel);
        }
        return $channel->getCCAccessToken();
    }
    /**
     * @param string $emailAddress
     * @param string $senderEmailAddress
     * @param string $senderFromName
     * @param string $subject
     * @param string $template
     * @return Response
     * @see https://v3.developer.constantcontact.com/api_guide/email_campaign_activity_test_send.html
     */
    public function test(
        string $accessToken,
        string $emailAddress,
        string $senderEmailAddress,
        string $senderFromName,
        string $subject,
        string $template
    ): Response {
        $campaignResponse = $this->createCampaign($accessToken, $senderEmailAddress, $senderFromName, $subject, $template);
        if ($campaignResponse->isError()) {
            return $campaignResponse;
        }
        $campaignResponseBody = $campaignResponse->getData();
        $campaignActivities = $campaignResponseBody->campaign_activities;
        if (!count($campaignActivities)) {
            return new Response('Internal Error on create Constant Contact campaign.', 500, $campaignResponseBody);
        }
        $activity_id = $campaignActivities[0]->campaign_activity_id;

        $testEndpoint = "{$this->getApiRoot()}/emails/activities/{$activity_id}/tests";
        $testRequestBody = [
            "email_addresses"   => [$emailAddress],
            "personal_message"  => $subject
        ];

        return $this->getResponseFromApi($testEndpoint, $accessToken, 'POST', $testRequestBody);
    }

    /**
     * @param string $accessToken
     * @param string $senderEmailAddress
     * @param string $senderName
     * @param string $subject
     * @param string $template
     * @return Response
     * @see https://v3.developer.constantcontact.com/api_guide/email_campaign_create.html
     */
    private function createCampaign(
        string $accessToken,
        string $senderEmailAddress,
        string $senderName,
        string $subject,
        string $template
    ): Response {
        $requestBody = [
            "name"                      => 'Campaign-' . $this->generateRandomString(),
            "email_campaign_activities" => [[
                "format_type"       => 5,
                "from_eamil"        => $senderEmailAddress,
                "reply_to_email"    => $senderEmailAddress,
                "from_name"         => $senderName,
                "subject"           => $subject,
                "html_content"      => $template,
                "pre_header"        => "",
            ]]
        ];
        $res = $this->getResponseFromApi($this->getApiRoot() . "/emails", $accessToken, "POST", $requestBody, 'Bearer');
        return $res;
    }

    /**
     * Here, we generate a medium-strength, 64-digit random string that we can use as
     * a key.
     *
     * @return string
     */
    private function generateRandomString(): string
    {
        $charactersToComposeKey = 'abcdefghiklmnopqrstuvwxyz0123456789';
        $randomStringFactory = new Factory();
        $randomStringGenerator = $randomStringFactory->getMediumStrengthGenerator();

        return $randomStringGenerator->generateString(12, $charactersToComposeKey);
    }
    /**
     * set Access token
     */
    public function setHeader($token): void
    {
        $this->header = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        ];
    }

    private function getOAuthApiRoot(): string
    {
        return env('CC_OAUTH_API_ROOT');
    }

    private function getApiRoot(): string
    {
        return env('CC_API_ROOT');
    }

    private function getRedirectUri(): string
    {
        return env('SERVICE_ARAGORN_URL') . '/settings';
    }

    private function getOAuth(): string
    {
        $api_key = env('CC_API_KEY');
        $secret_key = env('CC_SECRET_KEY');
        return base64_encode($api_key . ':' . $secret_key);
    }
}
