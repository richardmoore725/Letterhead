<?php

namespace App\Http\Controllers;

use App\Http\Services\AdServiceInterface;
use App\Models\Channel;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\PassportStamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;

/**
 * OrderController allows us to perform more complex "Ad Order Beacon" related services within the
 * platform, rather than simple GETs and POSTs that BeaconController handles.
 *
 * Class Order
 * @group Ad Management
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var BeaconServiceInterface
     */
    private $beaconService;

    public function __construct(BeaconServiceInterface $beaconService)
    {
        $this->beaconService = $beaconService;
    }


    public function getAds(Channel $channel): JsonResponse
    {
        $beaconSlug = 'ads';
        $restfulResourcePath = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads";

        $ads = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $channel->getBrandId(), $channel->getId(), $restfulResourcePath);

        if (empty($ads)) {
            return response()->json('Nada.', 404);
        }

        return response()->json($ads, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function getOrders(Channel $channel): JsonResponse
    {
        $beaconSlug = 'ads';
        $restfulResourcePath = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/orders";

        $orders = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $channel->getBrandId(), $channel->getId(), $restfulResourcePath);

        if (empty($orders)) {
            return response()->json("{$channel->getTitle()} has no orders placed yet.", 404);
        }

        return response()->json($orders, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
