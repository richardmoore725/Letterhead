<?php

namespace App\Task;

use App\Http\Services\ChannelServiceInterface;
use App\Jobs\SyncMailChimpListData;
use App\Models\Channel;
use App\DTOs\ChannelDto;
use App\Collections\ChannelCollection;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\JsonResponse;

class SyncMailchimpList
{
    private $channelService;

    /**
     * @var Queue
     */
    private $queue;

    public function __construct(
        ChannelServiceInterface $channelService,
        Queue $queue
    ) {
        $this->channelService = $channelService;
        $this->queue = $queue;
    }

    public function __invoke()
    {
        $channelsThatAutoSyncListStats = $this->channelService->getChannelsThatAutoSyncListStats();

        if (empty($channelsThatAutoSyncListStats)) {
            return response()->json('No channels that auto sync list stats found', 404);
        }

        $channels = $channelsThatAutoSyncListStats->getModels($channelsThatAutoSyncListStats->toArray());

        foreach ($channels as $channel) {
            $syncMailChimpListDataJob = new SyncMailChimpListData($channel);
            $this->queue->pushOn('mcstats', $syncMailChimpListDataJob);
        }
    }
}
