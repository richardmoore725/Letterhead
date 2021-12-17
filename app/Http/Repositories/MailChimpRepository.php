<?php

namespace App\Http\Repositories;

use App\DTOs\MailChimpListDto;
use App\DTOs\SegmentDto;
use App\Collections\ListCollection;
use App\Collections\SegmentCollection;
use App\Models\Letter;
use DrewM\MailChimp\MailChimp;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use App\Http\Response;

class MailChimpRepository extends BeaconRepository implements MailChimpRepositoryInterface
{
    private const API_ROOT = 'https://datacenter.api.mailchimp.com/3.0';

    /**
     * @param string $accessToken
     * @param Letter $letter
     * @param string $listId
     * @param int $segmentId
     * @param string $senderEmailAddress
     * @param string $senderName
     * @param string $subject
     * @return Response
     * @see https://mailchimp.com/developer/marketing/api/campaigns/add-campaign/
     */
    private function createCampaign(
        string $accessToken,
        Letter $letter,
        string $senderEmailAddress,
        string $senderName,
        string $subject
    ): Response {
        $endpoint = "{$this->getApiRoot($accessToken)}/campaigns";
        $requestBody = [
            'recipients' => [
                'list_id' => $letter->getEmailServiceProviderListId(),
                'segment_opts' => [
                    'saved_segment_id' => $letter->getSegmentId(),
                ],
            ],
            'settings' => [
                'from_name' => $senderName,
                'reply_to' => $senderEmailAddress,
                'subject_line' => $subject,
            ],
            'type' => 'regular',
        ];

        $response = $this->getResponseFromApi($endpoint, $accessToken, 'POST', $requestBody);

        return $this->parseResponseFromMailChimpQuery($response);
    }

    /**
     * Make a GET request against the MailChimp API.
     * @param MailChimp $mailChimp
     * @param string $endpoint
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    private function get(MailChimp $mailChimp, string $endpoint, array $arguments)
    {
        try {
            $results = $mailChimp->get($endpoint, $arguments);
            return $this->parseResponse($mailChimp, $results);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function getApiRoot(string $accessToken): string
    {
        $root = self::API_ROOT;
        list(, $datacenterFromAccessTokenString) = explode('-', $accessToken);
        $rootWithDataCenterFromToken = str_replace('datacenter', $datacenterFromAccessTokenString, $root);

        return $rootWithDataCenterFromToken;
    }

    /**
     * Parse the response from MailChimp's API and either throw an exception if
     * something goes awry or return the results.
     * @deprecated
     * @param MailChimp $mailChimp
     * @param $mailChimpApiResults
     * @return mixed
     * @throws \Exception
     */
    private function parseResponse(MailChimp $mailChimp, $mailChimpApiResults)
    {
        if ($mailChimpApiResults === false) {
            $errorMessage = $mailChimp->getLastError();
            throw new \Exception($errorMessage);
        }

        if (isset($mailChimpApiResults['status']) && $mailChimpApiResults['status'] >= 400) {
            $responseDetails = $mailChimpApiResults['details'] ?? '';
            throw new \Exception($responseDetails);
        }

        return $mailChimpApiResults;
    }

    private function parseResponseFromMailChimpQuery(Response $response)
    {
        if ($response->isSuccess()) {
            return $response;
        }

        $errorData = $response->getData();

        if (!isset($errorData->detail)) {
            return $response;
        }

        return new Response($errorData->detail, $errorData->status);
    }

    public function getListById(MailChimp $mailChimp, string $mailChimpListId): ?MailChimpListDto
    {
        try {
            $endpoint = "lists/{$mailChimpListId}";
            $arguments = [];

            $list = $this->get($mailChimp, $endpoint, $arguments);
            $listObject = json_decode(json_encode($list), false);

            return new MailChimpListDto($listObject);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    public function getListSegments(MailChimp $mailChimp, string $mailChimpListId): SegmentCollection
    {
        try {
            $endpoint = "lists/{$mailChimpListId}/segments";
            $arguments = [];

            $segments = $this->get($mailChimp, $endpoint, $arguments);

            $arrayOfSegmentObjects = json_decode(json_encode($segments['segments']), false);

            $segmentDtos = array_map(function (object $segmentObject) {
                $segmentDto = new SegmentDto($segmentObject);
                return $segmentDto;
            }, $arrayOfSegmentObjects);

            return new SegmentCollection($segmentDtos);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new SegmentCollection([]);
        }
    }

    public function getLists(MailChimp $mailChimp): ListCollection
    {
        try {
            $endpoint = "lists";
            $arguments = [
                'count' => 100,
            ];

            $lists = $this->get($mailChimp, $endpoint, $arguments);
            $arrayOfListObjects = json_decode(json_encode($lists['lists']), false);

            $listDtos = array_map(function (object $listObject) {
                $listDto = new MailChimpListDto($listObject);
                return $listDto;
            }, $arrayOfListObjects);

            return new ListCollection($listDtos);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new ListCollection([]);
        }
    }

    public function ping(MailChimp $mailChimp): Response
    {
        try {
            $endpoint = "ping";
            $responseArray = $mailChimp->get($endpoint);

            if (array_key_exists('health_status', $responseArray)) {
                return new Response('Everything\'s Chimpy!', 200);
            }

            return new Response($responseArray['title'], 401, $responseArray['detail']);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new Response('Caught this exception in ping', 500);
        }
    }

    /**
     * @param string $accessToken
     * @param Letter $letter
     * @param string $senderEmailAddress
     * @param string $senderFromName
     * @param string $subject
     * @param string $template
     * @return Response
     * @see https://mailchimp.com/developer/marketing/api/campaigns/send-campaign/
     */
    public function send(
        string $accessToken,
        Letter $letter,
        string $senderEmailAddress,
        string $senderFromName,
        string $subject,
        string $template
    ): Response {
        $campaignResponse = $this->createCampaign($accessToken, $letter, $senderEmailAddress, $senderFromName, $subject);

        if ($campaignResponse->isError()) {
            return $campaignResponse;
        }

        $campaignResponseBody = $campaignResponse->getData();

        if (!isset($campaignResponseBody->id)) {
            return new Response('MailChimp didn\'t return an `id` property as expected from a successful createCampaign post', 500, $campaignResponseBody);
        }

        $campaignId = $campaignResponseBody->id;

        $campaignTemplateResponse = $this->setCampaignTemplate($accessToken, $campaignId, $template);

        if ($campaignTemplateResponse->isError()) {
            return $campaignTemplateResponse;
        }

        $sendEndpoint = "{$this->getApiRoot($accessToken)}/campaigns/{$campaignId}/actions/send";
        $sendRequestBody = [];

        $sendResponse = $this->getResponseFromApi($sendEndpoint, $accessToken, 'POST', $sendRequestBody);

        return $this->parseResponseFromMailChimpQuery($sendResponse);
    }

    /**
     * @param string $accessToken
     * @param string $campaignId
     * @param string $template
     * @return Response
     * @see https://mailchimp.com/developer/marketing/api/campaign-content/set-campaign-content/
     */
    private function setCampaignTemplate(string $accessToken, string $campaignId, string $template): Response
    {
        $endpoint = "{$this->getApiRoot($accessToken)}/campaigns/{$campaignId}/content";
        $requestBody = [
            'html' => $template,
        ];

        return $this->getResponseFromApi($endpoint, $accessToken, 'PUT', $requestBody);
    }

    /**
     * @param string $accessToken
     * @param string $emailAddress
     * @param string $senderEmailAddress
     * @param Letter $letter
     * @param string $senderFromName
     * @param string $subject
     * @param string $template
     * @return Response
     * @see https://mailchimp.com/developer/marketing/api/campaigns/send-test-email/
     */
    public function test(
        string $accessToken,
        string $emailAddress,
        Letter $letter,
        string $senderEmailAddress,
        string $senderFromName,
        string $subject,
        string $template
    ): Response {
        $campaignResponse = $this->createCampaign($accessToken, $letter, $senderEmailAddress, $senderFromName, $subject);

        if ($campaignResponse->isError()) {
            return $campaignResponse;
        }

        $campaignResponseBody = $campaignResponse->getData();

        if (!isset($campaignResponseBody->id)) {
            return new Response('MailChimp didn\'t return an `id` property as expected from a successful createCampaign post', 500, $campaignResponseBody);
        }

        $campaignId = $campaignResponseBody->id;

        $campaignTemplateResponse = $this->setCampaignTemplate($accessToken, $campaignId, $template);

        if ($campaignTemplateResponse->isError()) {
            return $campaignTemplateResponse;
        }

        $testEndpoint = "{$this->getApiRoot($accessToken)}/campaigns/{$campaignId}/actions/test";
        $testRequestBody = [
            'send_type' => 'html',
            'test_emails' => [
                $emailAddress,
            ],
        ];

        return $this->getResponseFromApi($testEndpoint, $accessToken, 'POST', $testRequestBody);
    }
}
