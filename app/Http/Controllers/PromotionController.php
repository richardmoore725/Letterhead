<?php

namespace App\Http\Controllers;

use App\Collections\PromotionCollection;
use App\Events\PromotionStatusChangedEvent;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\MessageServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\Message;
use App\Models\PassportStamp;
use App\Models\Promotion;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PromotionController extends Controller
{
    public function createPendingPromotion(
        AdServiceInterface $adService,
        Channel $channel,
        Event $event,
        Request $request
    ): JsonResponse {
        $promotionFormattedForMultipartPost = $adService->getAdRequestFormattedForMultipartPost($channel, $request);
        $promotionServiceResponse = $adService->createPromotion($channel, $promotionFormattedForMultipartPost);

        if ($promotionServiceResponse->isError()) {
            return $promotionServiceResponse->getJsonResponse();
        }

        /**
         * @var Promotion $promotion
         */

        $promotion = $promotionServiceResponse->getData();

        $this->triggerPromotionStatusChangedEvent($event, $promotion);

        return response()->json($promotion->convertToArray(), 201);
    }

    public function getPromotions(AdServiceInterface $adService, Channel $channel, Request $request): JsonResponse
    {
        $date = $request->input('date', '');
        $renderMetricsAndHtml = $request->boolean('resolveContent', false);
        $renderMjml = $request->boolean('mjml', false);
        $status = (int) $request->input('status', Promotion::STATUS_NEWLY_CREATED);
        $serviceResponse = $adService->getPromotions($channel->getBrandId(), $channel->getId(), $date, $renderMetricsAndHtml, $renderMjml, $status);

        if ($serviceResponse->isError()) {
            return $serviceResponse->getJsonResponse();
        }

        /**
         * @var PromotionCollection $promotionCollection
         */
        $promotionCollection = $serviceResponse->getData();

        return response()->json($promotionCollection->getPublicArray());
    }

    public function getPromotionsFeed(
        AdServiceInterface $adService,
        ChannelServiceInterface $channelService,
        Request $request,
        View $view
    ): Response {
        $brandApiKey = $request->input('key', '');
        $date = $request->input('date', '');

        if (empty($brandApiKey)) {
            return new Response('We need a key to show you this.', 400);
        }

        if (empty($date)) {
            return new Response('At the moment, we only provide feeds for specific dates.', 400);
        }

        $channelServiceResponse = $channelService->getChannelByBrandApiKey($brandApiKey);

        if ($channelServiceResponse->isError()) {
            return new Response('Unfortunately we could not find this channel.', $channelServiceResponse->getStatus());
        }

        /**
         * @var Channel $channel
         */
        $channel = $channelServiceResponse->getData();
        $serviceResponse = $adService->getPromotions($channel->getBrandId(), $channel->getId(), $date, true, false, Promotion::STATUS_NEWLY_CREATED);

        if ($serviceResponse->isError()) {
            return new Response('We had trouble fetching promotions.', $serviceResponse->getStatus());
        }

        /**
         * @var PromotionCollection $promotionCollection
         */
        $promotionCollection = $serviceResponse->getData();
        $promotions = $promotionCollection->getModels();

        $feed = $view->make('promotions/promotions-feed', [
            'channel' => $channel,
            'promotions' => $promotions,
        ]);

        return response($feed)->header('Content-Type', 'application/xml');
    }

    public function updatePromotionStatusToApproved(
        AdServiceInterface $adService,
        Event $event,
        MessageServiceInterface $messageService,
        PassportStamp $passport,
        Promotion $promotion,
        Request $request
    ): JsonResponse {
        $promotion = $adService->updatePromotionStatus($promotion, Promotion::STATUS_APPROVED_FOR_PUBLICATION);

        $message = is_null($request->input('message')) ? $request->input('message') : 'This promotion has been approved.';
        $userId = $passport->getId();

        $response = $messageService->createMessage(
            $message,
            $promotion->getId(),
            'promotion',
            $userId
        );

        if ($response->isError()) {
            return response()->json("Unfortunately, we couldn't save the message.", 500);
        }

        $message = $response->getData();

        $this->triggerPromotionStatusChangedEvent($event, $promotion);

        $promotionWithMessage = [
            "promotion" => $promotion->convertToArray(),
            "message" => $message,
        ];

        return response()->json($promotionWithMessage, 200);
    }

    public function updatePromotionStatusToRequestChanges(
        AdServiceInterface $adService,
        Event $event,
        MessageServiceInterface $messageService,
        PassportStamp $passport,
        Promotion $promotion,
        Request $request
    ): JsonResponse {
        $message = $request->input('message');

        if (empty($message)) {
            return response()->json('A change request requires a message', 400);
        }

        $promotion = $adService->updatePromotionStatus($promotion, Promotion::STATUS_CHANGES_REQUESTED);

        $userId = $passport->getId();

        $response = $messageService->createMessage(
            $message,
            $promotion->getId(),
            'promotion',
            $userId
        );

        if ($response->isError()) {
            return response()->json("Unfortunately, we couldn't save the message.", 500);
        }

        $message = $response->getData();

        $this->triggerPromotionStatusChangedEvent($event, $promotion);

        $promotionWithMessage = [
            "promotion" => $promotion->convertToArray(),
            "message" => $message,
        ];

        return response()->json($promotionWithMessage, 200);
    }

    private function triggerPromotionStatusChangedEvent(Event $event, Promotion $promotion)
    {
        return $event->dispatch(new PromotionStatusChangedEvent($promotion));
    }
}
