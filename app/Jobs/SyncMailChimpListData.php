<?php

namespace App\Jobs;

use App\Http\Services\BrandServiceInterface;
use App\Http\Services\MailChimpFacade;
use App\Models\Channel;
use App\Models\Configuration;
use App\Models\MailChimpList;
use App\Collections\ChannelConfigurationCollection;
use Carbon\CarbonImmutable;
use DrewM\MailChimp\MailChimp;
use Illuminate\Contracts\Queue\Queue;

class SyncMailChimpListData extends Job
{
    private $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function handle(
        BrandServiceInterface $brandService,
        Queue $queue
    ): void {
        $mailChimpFacade = $this->channel->getMailChimp();

        if (empty($mailChimpFacade)) {
            return;
        }

        $selectedMailChimpListId = $this->channel->getChannelConfigurations()->getMcSelectedEmailListId();
        $mailChimpList = $mailChimpFacade->getListById($selectedMailChimpListId);

        $channelId = $this->channel->getId();

        if (empty($mailChimpList)) {
            /**
             * Remove the failed API key from the configurations
             */
            $apiKeyConfiguration = $brandService->getConfigurationBySlug('mcApiKey');
            $mailChimpListConfiguration = $brandService->getConfigurationBySlug('mcSelectedEmailListId');
            $integrationEnabledConfiguration = $brandService->getConfigurationBySlug('mcIntegration');

            $brandService->updateChannelConfiguration($channelId, '', $apiKeyConfiguration->getId());
            $brandService->updateChannelConfiguration($channelId, '', $mailChimpListConfiguration->getId());
            $brandService->updateChannelConfiguration($channelId, 0, $integrationEnabledConfiguration->getId());

            return;
        }

        $clickthroughRateConfiguration = $brandService->getConfigurationBySlug('clickthroughRate');
        $openRateConfiguration = $brandService->getConfigurationBySlug('openRate');
        $totalSubscribersConfiguration = $brandService->getConfigurationBySlug('totalSubscribers');

        $brandService->updateChannelConfiguration($channelId, $mailChimpList->getClickRate(), $clickthroughRateConfiguration->getId());
        $brandService->updateChannelConfiguration($channelId, $mailChimpList->getOpenRate(), $openRateConfiguration->getId());
        $brandService->updateChannelConfiguration($channelId, $mailChimpList->getTotalSubscribers(), $totalSubscribersConfiguration->getId());
    }
}
