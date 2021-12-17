<?php

namespace App\Http\Repositories;

use App\Collections\ChannelSubscriberCollection;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use App\Http\Response;

class LetterheadEspRepository implements LetterheadEspRepositoryInterface
{
    public const TABLE_CHANNEL_SUBSCRIBERS = 'channel_subscribers';
    public const TABLE_CHANNEL_SUBSCRIPTIONS = 'channel_subscriptions';

    public function getSubscribersByChannel(int $channelId): Response
    {
        $subscribersTable = self::TABLE_CHANNEL_SUBSCRIBERS;
        $subscriptionsTable = self::TABLE_CHANNEL_SUBSCRIPTIONS;

        try {
            $queryResults = app('db')
                ->table($subscribersTable)
                ->join($subscriptionsTable, "{$subscribersTable}.id", '=', "{$subscriptionsTable}.channelSubscriberId")
                ->where("{$subscribersTable}.channelId", '=', $channelId)
                ->select(
                    "{$subscribersTable}.channelId",
                    "{$subscriptionsTable}.status",
                    "{$subscribersTable}.created_at",
                    "{$subscribersTable}.deletedAt",
                    "{$subscribersTable}.email",
                    "{$subscribersTable}.id",
                    "{$subscribersTable}.name",
                    "{$subscribersTable}.updated_at",
                    "{$subscribersTable}.userId",
                )
                ->get();

            $subscribers = new ChannelSubscriberCollection($queryResults);
            return new Response('', 200, $subscribers);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            $subscribers = new ChannelSubscriberCollection([]);
            return new Response($e->getMessage(), 500, $subscribers);
        }
    }
}
