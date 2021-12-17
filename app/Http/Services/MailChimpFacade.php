<?php

namespace App\Http\Services;

use App\DTOs\MailChimpListDto;
use App\Http\Repositories\MailChimpRepository;
use App\Http\Repositories\MailChimpRepositoryInterface;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\MailChimpList;
use App\Collections\ListCollection;
use DrewM\MailChimp\MailChimp;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Carbon\CarbonImmutable;

/**
 * Class MailChimpFacade
 * @package App\Http\Services
 */
class MailChimpFacade implements MailChimpFacadeInterface
{
    private $mailchimp;
    private $repository;

    public function __construct(MailChimp $mailChimp, MailChimpRepositoryInterface $repository)
    {
        $this->mailchimp = $mailChimp;
        $this->repository = $repository;
    }

    /**
     * This static method helps instantiate a MailChimp instance through the
     * DrewM API library, provided that the Channel has a valid API key.
     *
     * @param Channel $channel
     * @return MailChimpFacade|null
     */
    public static function createFromChannel(Channel $channel): ?MailChimpFacade
    {
        $channelConfigurations = $channel->getChannelConfigurations();
        $apiKey = $channelConfigurations->getMcApiKey();

        try {
            $mailChimp = new MailChimp($apiKey);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }

        /**
         * We abstract some of our code into two parts, a service and a repository, the
         * latter we can focus responsibility to interfacing with the third-party API itself,
         * transforming, and encoding, whereas we can use the service for orchestration.
         */
        $client = new Client();
        $request = new Request();
        $repository = new MailChimpRepository($client, $request);
        return new MailChimpFacade($mailChimp, $repository);
    }

    public function sendTestEmail(string $to, string $from, string $fromName, string $subject, string $htmlContents): Response
    {
        try {
            $id = $this->createRegularCampaignWithoutList($subject, $from, $fromName);

            if (empty($id)) {
                Rollbar::log(Level::ERROR, "Failed to create mailchimp campaign.");
                return new Response('Failed to create mailchimp campaign. Please make sure you are using a valid Mailchimp api key.', 500);
            }

            $success = $this->setCampaignContent($id, $htmlContents);

            if (!$success) {
                Rollbar::log(Level::ERROR, "Failed to set mailchimp campaign contents.");
                return new Response('Failed to set mailchimp campaign contents. Please make sure you are using a valid Mailchimp api key.', 500);
            }
            $result = $this->mailchimp->post("campaigns/$id/actions/test", [
                'test_emails' => [$to],
                'send_type'   => 'html'
            ]);

            if (isset($result['status'])) {
                return new Response($result['detail'], $result['status']);
            }

            return new Response();
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new Response($e->getMessage(), 500);
        }
    }

    public function sendEmailForChannel(Letter $letter, Channel $channel, string $subject, string $htmlContents): Response
    {
        $channelConfigurations = $channel->getChannelConfigurations();
        $listId = $channelConfigurations->getMcSelectedEmailListId();
        $fromEmail = $channelConfigurations->getDefaultFromEmailAddress();
        $fromName = $channelConfigurations->getDefaultEmailFromName();
        $segmentId = $letter->getSegmentId();

        // this really should be reported in some manner
        if (empty($listId) || empty($fromEmail) || empty($fromName)) {
            Rollbar::log(Level::ERROR, "channel configuration error:  No default list id, no fromEmail or no fromName.");
            return new Response('channel configuration error:  No default list id, no fromEmail or no fromName.', 500);
        }

        $id = $this->createRegularCampaignForList($listId, $segmentId, $subject, $fromEmail, $fromName);

        if (empty($id)) {
            Rollbar::log(Level::ERROR, "Failed to create mailchimp campaign for list $listId");
            return new Response('Failed to create mailchimp campaign', 500, $listId);
        }

        $success = $this->setCampaignContent($id, $htmlContents);

        if (!$success) {
            Rollbar::log(Level::ERROR, "Failed to create mailchimp campaign for list $listId");
            return new Response('Failed to create mailchimp campaign', 500, $listId);
        }

        $result = $this->mailchimp->post("campaigns/$id/actions/send");

        // failure won't result in a boolean return
        $isResultTrue = $result === true;


        // Reponse {
            // $reason(string),
            // $status(200/500),
            // $data -> we set campaignId here.
        // }
        return new Response($isResultTrue ? 'true' : 'false', $isResultTrue ? 200 : 500, $id);
    }

    public function sendCampaign(string $campaignId): bool
    {
        $this->canUseMailChimpOrThrow();

        $result = $this->mailchimp->post("campaigns/$campaignId/actions/send");

        // NOTE:  On failure result will not be a boolean, hence this check
        //
        return $result === true;
    }

    public function setCampaignContent(string $campaignId, string $htmlContent): bool
    {
        $this->canUseMailChimpOrThrow();

        $result = $this->mailchimp->put("campaigns/$campaignId/content", [
            'html' => $htmlContent,
        ]);


        // NOTE:  We're only checking if html exists as a proxy for testing if
        //        the call succeeded or not.
        //        We also don't test for empty here because empty HTML is valid
        //        content, albeit probably not what is wanted.
        // TODO:  We really need to figure out what our error strategy is going to be.
        //        This is basically hiding errors from mailchimp and over time will
        //        make the app difficult to debug and difficult to stabilize.
        //
        if (!isset($result['html'])) {
            Rollbar::log(Level::ERROR, "Failed setting campaign content:  campaign id => $campaignId");
            return false;
        }
        return true;
    }

    public function createRegularCampaignForList(string $listId, int $segmentId, string $subject, string $replyTo, string $fromName): ?string
    {
        return $this->createCampaignForList('regular', $listId, $segmentId, $subject, $replyTo, $fromName);
    }

    public function createRegularCampaignWithoutList(string $subject, string $replyTo, string $fromName): ?string
    {
        return $this->createCampaignWithoutList('regular', $subject, $replyTo, $fromName);
    }

    private function createCampaignForList(string $type, string $listId, int $segmentId, string $subject, string $replyTo, string $fromName): ?string
    {
        try {
            $this->canUseMailChimpOrThrow();
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }

        $result = $this->mailchimp->post("campaigns", [
            'type' => $type,
            'recipients' => [
                'list_id' => $listId,
                'segment_opts' => [
                    'saved_segment_id' => $segmentId,
                ],
            ],
            'settings' => [
                'subject_line' => $subject,
                'reply_to' => $replyTo,
                'from_name' => $fromName
            ],
        ]);

        if (!isset($result['id']) || empty($result['id'])) {
            Rollbar::log(Level::ERROR, "Failed creating a mailchimp campaign:  listId => $listId");
            return null;
        }

        return $result['id'];
    }

    private function createCampaignWithoutList(string $type, string $subject, string $replyTo, string $fromName): ?string
    {
        try {
            $this->canUseMailChimpOrThrow();
        } catch (\Exception $e) {
        }

        $result = $this->mailchimp->post("campaigns", [
            'type' => $type,
            'settings' => [
                'subject_line' => $subject,
                'reply_to' => $replyTo,
                'from_name' => $fromName
            ],
        ]);

        if (!isset($result['id']) || empty($result['id'])) {
            Rollbar::log(Level::ERROR, "Failed creating a mailchimp campaign:  fromEmail => $replyTo");
            return null;
        }
        return $result['id'];
    }

    public function getListById(string $mailChimpListId): ?MailChimpList
    {
        $listDto = $this->repository->getListById($this->mailchimp, $mailChimpListId);

        if (empty($listDto)) {
            return null;
        }

        $segments = $this->repository->getListSegments($this->mailchimp, $mailChimpListId);

        $listModel = new MailChimpList($listDto);
        $listModel->setSegments($segments);

        return $listModel;
    }

    /**
     * Return an array of MailChimpLists accessible by the given MailChimp API key.
     *
     * @uses ListCollection
     * @uses MailChimpList
     * @return ListCollection
     */
    public function getLists(): ListCollection
    {
        $lists = $this->repository->getLists($this->mailchimp);

        return new ListCollection($lists);
    }

    private function canUseMailChimpOrThrow()
    {
        if (empty($this->mailchimp)) {
            throw new \Exception("Unable to communicate with MailChimp.  This is most likely due to an empty ApiKey.");
        }
    }

    public function ping(): Response
    {
        return $this->repository->ping($this->mailchimp);
    }

    public function checkMailChimpHealthIfNeeded(Channel $channel): bool
    {
        /**
         * We need to ping() to check mc health in 2 cases:
         * 1. The channel never pinged before;
         * 2. Last pinged time was 1hr ago;
        **/

        /* Case 1: */
        $timeSinceMailChimpStatusPinged = $channel->getTimeSinceMailChimpStatusPinged();
        $firstTimeToPing = !$channel->getHasValidMailChimpKey() && empty($timeSinceMailChimpStatusPinged);

        /* Case 2: */
        $hasBeenAnHourSinceLastPing = CarbonImmutable::parse($timeSinceMailChimpStatusPinged)->diffInHours(CarbonImmutable::now()) > 1;
        $needPingToUpdate = !empty($timeSinceMailChimpStatusPinged) && $hasBeenAnHourSinceLastPing;

        return $firstTimeToPing || $needPingToUpdate;
    }
}
