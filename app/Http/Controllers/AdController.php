<?php

namespace App\Http\Controllers;

use App\DTOs\PromotionDto;
use App\Events\OrderPurchasedEvent;
use App\Events\PromotionPublishedEvent;
use App\Events\PromotionScheduledEvent;
use App\Events\PromotionRescheduledEvent;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\DiscountCodeServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\PassportStamp;
use App\Models\Promotion;
use App\Models\DiscountCode;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;

/**
 * AdController allows us to perform more complex "Ad Beacon" related services within the
 * platform, rather than simple GETs and POSTs that BeaconController handles.
 *
 * Class AdController
 * @deprecated
 * @group Ad Management
 * @package App\Http\Controllers
 */
class AdController extends Controller
{
    /**
     * @var AdServiceInterface
     */
    private $adService;
    private $beaconService;
    private $brandService;
    private $channelService;
    private $discountCodeService;
    private $event;
    private $userService;

    public function __construct(
        AdServiceInterface $adService,
        BeaconServiceInterface $beaconService,
        BrandServiceInterface $brandService,
        ChannelServiceInterface $channelService,
        DiscountCodeServiceInterface $discountCodeService,
        Event $event,
        UserServiceInterface $userService
    ) {
        $this->adService = $adService;
        $this->beaconService = $beaconService;
        $this->brandService = $brandService;
        $this->channelService = $channelService;
        $this->discountCodeService = $discountCodeService;
        $this->event = $event;
        $this->userService = $userService;
    }

    /**
     * This method will trigger a PromotionPublishedEvent in the system if a valid
     * promotion comes across the wire.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function broadcastPromotionPublishedEvent(Request $request): JsonResponse
    {
        $promotion = $this->getPromotionFromAdService($request);

        if (empty($promotion)) {
            Rollbar::log(Level::WARNING, "Our promotion published webhook was hit without a valid promotion.");
            return response()->json('', 400);
        }

        $userId = $request->input('userId');

        $this->event->dispatch(new PromotionPublishedEvent($promotion, $userId));

        return response()->json('Broadcast intercepted :).', 200);
    }

    /**
     * This method will trigger a PromotionRescheduledEvent in the system if a valid
     * promotion comes across the wire.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function broadcastPromotionRescheduledEvent(Request $request): JsonResponse
    {
        $promotion = $this->getPromotionFromAdService($request);

        if (empty($promotion)) {
            Rollbar::log(Level::WARNING, "Our promotion published webhook was hit without a valid promotion.");
            return response()->json('', 400);
        }

        $userId = $request->input('userId');

        $this->event->dispatch(new PromotionRescheduledEvent($promotion, $userId));

        return response()->json('Broadcast intercepted :).', 200);
    }

    private function getPromotionFromAdService(Request $request): ?Promotion
    {
        $promotionFromRequest = $request->input('promotion');

        if (empty($promotionFromRequest)) {
            return null;
        }

        $promotionDto = new PromotionDto($promotionFromRequest);
        $promotion = $promotionDto->convertToModel();

        return $promotion;
    }

    /**
     * @deprecated
     * @param Request $request
     * @param int $brandId
     * @param int $channelId
     * @param PassportStamp $passport
     * @return JsonResponse
     */
    public function createBrandChannelAd(Channel $channel, PassportStamp $passport, Request $request)
    {
        $adServicePostDataArray = $this->adService->getAdRequestFormattedForMultipartPost($channel, $request);

        $beaconSlug = 'ads';
        $restfulResourcePath = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads";

        $adCreated = $this->beaconService->createResourceByBeaconSlug(
            $beaconSlug,
            $channel->getBrandId(),
            $channel->getId(),
            $restfulResourcePath,
            $adServicePostDataArray,
            true
        );

        if (empty($adCreated)) {
            return response()->json('We were not able to create your ad, hmm.', 500);
        }

        /**
         * Someone just successfully scheduled a new promotion, so we should celebrate
         * by triggering an event : ).
         */
        $this->event->dispatch(new PromotionScheduledEvent($channel, $passport, $adCreated));

        return response()->json($adCreated, 200, [], JSON_UNESCAPED_SLASHES);
    }

    private function getPromotionCreditInfoByPromotionId(int $adId): JsonResponse
    {
        $promotionCredit = $this->adService->getPromotionCreditByPromotionId($adId);

        if (empty($promotionCredit)) {
            return response()->json('We could not find a promotion with that ID.', 404);
        }

        $user = $this->userService->getUserById($promotionCredit->getUserId());

        $userEmail = empty($user) ? '' : $user->getEmail();
        $userName = empty($user) ? '' : $user->getName();

        $promotionCredit->setUserEmail($userEmail);
        $promotionCredit->setUserName($userName);

        return response()->json($promotionCredit->convertToArray(), 200);
    }

    public function getAdCreditsByUserId(Request $request, int $id): JsonResponse
    {
        $beaconSlug = 'ads';
        $brandId = 0;
        $channelId = 0;
        $restfulResourcePath = "users/{$id}/ad-credits";

        $adCredits = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $brandId, $channelId, $restfulResourcePath);

        return response()->json($adCredits);
    }

    /**
     * @note use PromotionController::getPromotions
     * @deprecated
     * @param Request $request
     * @param Channel $channel
     * @return JsonResponse
     */
    public function getAds(Request $request, Channel $channel): JsonResponse
    {
        $beaconSlug = 'ads';
        $date = $request->input('date', '');
        $dateQueryParameter = empty($date) ? '' : "&date={$date}";
        $mjmlQueryParameter = $request->input('mjml', 'false');
        $resolveContent = $request->input('resolveContent', 'false');
        $status = $request->input('status', Promotion::STATUS_NEWLY_CREATED);

        $restfulResourcePath = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads?resolveContent={$resolveContent}{$dateQueryParameter}&mjml={$mjmlQueryParameter}&status={$status}";

        $ads = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $channel->getBrandId(), $channel->getId(), $restfulResourcePath);

        if (empty($ads)) {
            return response()->json('Nada.', 404);
        }

        return response()->json($ads, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function getAdTypesByChannelId(Request $request, int $channelId): JsonResponse
    {
        $beaconSlug = 'ads';
        $brandId = 0;
        $restfulResourcePath = "brands/{$brandId}/channels/{$channelId}/ads/types";

        $adTypes = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $brandId, $channelId, $restfulResourcePath);

        return response()->json($adTypes);
    }

    public function getOrdersByUserId(Request $request, int $id): JsonResponse
    {
        $beaconSlug = 'ads';
        $brandId = 0;
        $channelId = 0;
        $restfulResourcePath = "users/{$id}/orders";

        $orders = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $brandId, $channelId, $restfulResourcePath);

        return response()->json($orders);
    }

    public function getPromotionMetricsByChannelId(Channel $channel): JsonResponse
    {
        $test = $this->adService->getPromotionMetricsByChannelId($channel);

        return response()->json($test);
    }

    /**
     * Place an advertising order
     *
     * We use this method to place an order on an ad package for a user and
     * charge them.
     *
     * @authenticated
     *
     * @bodyParam amount required The amount to charge a user
     * @bodyParam description required The description of the package or order
     * @bodyParam packageId required An integer that identifies the specific package
     * @bodyParam paymentMethod required A payment-intent ID from Stripe
     * @bodyParam user required The ID of the user placing the order.
     *
     * @param Request $request
     * @param int $brandId
     * @param int $channelId
     * @return JsonResponse
     */
    public function orderAdvertisingPackage(Request $request, int $brandId, int $channelId, PassportStamp $passport): JsonResponse
    {
        /**
         * First we'll pull all the parameters from the header and ensure they exist. If not,
         * we'll return a 400.
         */
        $costOfPackage = $request->input('amount');
        $descriptionOfPackage = $request->input('description');
        $packageId = $request->input('packageId');
        $paymentMethod = $request->input('paymentMethod');
        $originalPurchasePrice = $request->input('originalPurchasePrice');
        $userId = $request->input('userId');
        $company = $request->input('userName');
        $discountCodeString = $request->input('discountCode', '');

        $discountCode = empty($discountCodeString) ?
                            null :
                            $this->discountCodeService->getDiscountCodeByCode($discountCodeString);

        $discountValue = empty($discountCode) ? 0 : $discountCode->getDiscountValue();
        $discountCodeId = empty($discountCode) ? 0 : $discountCode->getId();

        if (
            is_null($costOfPackage) ||
            empty($descriptionOfPackage) ||
            empty($packageId) ||
            empty($paymentMethod) ||
            is_null($originalPurchasePrice) ||
            empty($userId) ||
            empty($company)
        ) {
            return response()->json('Remember to send over an amount, description, packageId, paymentMethod, originalPurchasePrice, userId, and userName', 400);
        }

        $finalPriceOfPackage = (int) floor(( 1 - $discountValue / 100 ) * $costOfPackage);

        $brand = $this->brandService->getBrandById($brandId);
        if (empty($brand)) {
            return response()->json('Sorry the brand doesn\'t exist.', 404);
        }

        $brandConfigurations = $brand->getBrandConfigurations();
        $revenueShare = $brandConfigurations->getAdvertisingRevenueShare();
        $connectedStripeAccountId = $brandConfigurations->getStripeAccount();

        if (empty($connectedStripeAccountId)) {
            Rollbar::log(Level::ERROR, "Advertisers are trying to buy ads from the channel #{$channelId}, but their Stripe account hasn't been connected.");
            return response()->json('Unfortunately this brand hasn\'t finished setting up', 403);
        }

        $channel = $this->channelService->getChannelById($channelId);
        if (empty($channel)) {
            return response()->json('Sorry the channel doesn\'t exist.', 404);
        }

        /**
         * We want to create the Order object in AdService first. If unsuccessful then BeaconService will
         * return empty, and we can let the user know something went wrong.
         */
        $adsBeaconSlug = 'ads';
        $adServiceResourcePath = 'orders';
        $adServicePostDataArray = [
            'amount' => $finalPriceOfPackage,
            'brandId' => $brandId,
            'channelId' => $channelId,
            'company' => $company,
            'discountCodeId' => $discountCodeId,
            'discountValue' => $discountValue,
            'package_id' => $packageId,
            'platformUserId' => $userId,
            'originalPurchasePrice' => $originalPurchasePrice,
            'stripe_id' => $paymentMethod,
            'revenueShare' => $revenueShare,
        ];

        $order = $this->beaconService->createResourceByBeaconSlug($adsBeaconSlug, $brandId, $channelId, $adServiceResourcePath, $adServicePostDataArray, false);

        if (empty($order)) {
            return response()->json('We weren\'t able to save your order.', 500);
        }

        if (empty($finalPriceOfPackage)) {
            $response = [
                'invoice' => [],
                'order' => $order,
            ];

            $this->event->dispatch(new OrderPurchasedEvent(
                $company,
                $channel,
                $order->created,
                $order->id,
                $order->package->name,
                $costOfPackage,
                $discountValue,
                0,
                $passport
            ));

            return response()->json($response);
        }

        $applicationFeeAmount = (int) floor($revenueShare * $finalPriceOfPackage);

        /**
         * Once the order is placed we want to actually charge the user for it
         * given their Stripe Payment Method / Payment Intent ID. This goes to UserService. If this
         * fails, we will let the user know and return a 500.
         */
        $usersBeaconSlug = 'users';
        $userServiceResourcePath = "users/{$userId}/charges";
        $userServicePostDataArray = [
            'amount' => $finalPriceOfPackage,
            'paymentMethod' => $paymentMethod,
            'currency' => 'usd',
            'applicationFeeAmount' => $applicationFeeAmount,
            'connectedStripeAccountId' => $connectedStripeAccountId,
            'description' => $descriptionOfPackage
        ];

        $invoice = $this->beaconService->createResourceByBeaconSlug($usersBeaconSlug, $brandId, $channelId, $userServiceResourcePath, $userServicePostDataArray, false);

        if (empty($invoice)) {
            return response()->json('We weren\'t able to successfully charge you.', 500);
        }

        /**
         * On the bright side, if everything is groovy we'll return a 200 and send the user both
         * a copy of the invoice and a copy of the order.
         */
        $response = [
            'invoice' => $invoice,
            'order' => $order,
        ];

        $this->event->dispatch(new OrderPurchasedEvent(
            $company,
            $channel,
            $order->created,
            $order->id,
            $order->package->name,
            $costOfPackage,
            $discountValue,
            $finalPriceOfPackage,
            $passport
        ));

        return response()->json($response);
    }

    /**
     * Place an single promotion order
     *
     * We use this method to get or create a user, charge them for the promotion
     * place an order on a single-promotion package, and then book a promotion.
     *
     */
    public function orderSinglePromotionPackage(
        Brand $brand,
        Channel $channel,
        DiscountCode $discountCodeObject,
        int $amount,
        string $dateStart,
        string $paymentMethod,
        int $originalPurchasePrice,
        int $promotionTypeId,
        string $userEmail,
        string $userName
    ): JsonResponse {
        /**
         * Let's get what we need together.
         */
        $brandConfigurations = $brand->getBrandConfigurations();
        $brandId = $brand->getId();
        $channelId = $channel->getId();
        $descriptionOfPackage = "Single-promotion package of type no. {$promotionTypeId} for {$userName}";
        $discountValue = $discountCodeObject->getDiscountValue();
        $discountCodeId = $discountCodeObject->getId();

        /**
         * Need to grab a couple of configurations
         */
        $connectedStripeAccountId = $brandConfigurations->getStripeAccount();
        $revenueShare = $brandConfigurations->getAdvertisingRevenueShare();

        /**
         * Let's math some numbers.
         */
        $finalPriceOfPackage = $this->adService->calculateFinalPriceOfPackage($amount, $discountValue);

        $applicationFeeAmount = $this->adService->calculateApplicationFeeAmount($finalPriceOfPackage, $revenueShare);

        /**
         * Upfront, we'll make sure they have an account and we charge the user
         */
        $user = $this->userService->getOrCreateAndChargeUser(
            $applicationFeeAmount,
            $connectedStripeAccountId,
            $descriptionOfPackage,
            $finalPriceOfPackage,
            $paymentMethod,
            $userEmail,
            $userName
        );

        if (empty($user)) {
            Rollbar::log(Level::ERROR, "We weren\'t able to find or create this user with {$userEmail} and {$userName}, nor charge their account.");
            return response()->json('We weren\'t able to find or create this user, nor charge their account.', 500);
        }

        $userId = $user->getId();

        /**
         * Now that we've charged the user, let's place create a package,
         * place an order, and book a promotion.
         */
        $order = $this->adService->orderAndBookSinglePromotion(
            $brandId,
            $channelId,
            $finalPriceOfPackage,
            $dateStart,
            $discountCodeId,
            $discountValue,
            $userId,
            $originalPurchasePrice,
            $paymentMethod,
            $promotionTypeId,
            $revenueShare,
            $userName
        );

        if (empty($order)) {
            Rollbar::log(Level::WARNING, "We weren\'t able to create this order placed by user {$userId} for a promo type {$promotionTypeId} credit.");
            return response()->json('We weren\'t able to save your order.', 500);
        }

        /**
         * Returns an object based off an Order model:
         *
         * {
         *  "ads": [ {$promotionObject}],
         *  "amount": int,
         *  "amountAsCurrency": string,
         *  "brandId": int,
         *  "channelId": int,
         *  "created": string,
         *  "company": string,
         *  "discountCodeId": int,
         *  "discountValue": int,
         *  "id": int,
         *  "package": {$packageObject},
         *  "packageId": int,
         *  "finalAmount": int,
         *  "original_purchase_price": int,
         *  "platformUserId": int,
         *  "revenueShare": float,
         *  "ownerId": int,
         *  "orderDate": string,
         *  "updated": string"
         * }
         *
         */
        return response()->json($order);
    }

    public function orderAdsFromPipedrive(Request $request): JsonResponse
    {
        $pipedriveId = $request->input('pipedriveId');
        $email = $request->input('userEmail');
        $name = $request->input('userName');
        $adTypesInPackage = $request->input('adTypesInPackage');
        $price = $request->input('price');
        $revenueShare = $request->input('revenueShare');
        $brandId = $request->input('brandId', 0);
        $channelId = $request->input('channelId', 0);

        if (
            empty($pipedriveId)
            || empty($email)
            || empty($name)
            || empty($adTypesInPackage)
            || empty($price)
            || empty($revenueShare)
        ) {
            return response()->json('Remember to send over channelId, brandId, pipedriveId, email, name, adTypesInPackage, revenuShare and price.', 400);
        }

        $adTypeIdArray = array_map(
            function ($adTypeInPackage) {
                return $adTypeInPackage['adTypeId'];
            },
            $adTypesInPackage
        );

        /**
         * get brandId and channelId from adTypeId in adTypesInPackage
         **/
        $beaconService = $this->beaconService;

        try {
            $adTypesArray = array_map(
                function ($adTypeId) use ($beaconService) {
                    $adBeaconSlug = 'ads';
                    $adServiceResourcePath = "ad-types/{$adTypeId}";
                    $adType = $beaconService->getAdResourceByBeaconSlug(
                        $adBeaconSlug,
                        $adServiceResourcePath
                    );

                    if (empty($adType)) {
                        throw new \Exception('Nope');
                    }

                    return  $adType;
                },
                $adTypeIdArray
            );
        } catch (\Exception $e) {
            return response()->json('this adType doesn\'t exist.', 500);
        }

            $collectionOfAdTypes = collect($adTypesArray);
            $firstAdType = $collectionOfAdTypes->first();

        /**
         * We can't create packages and orders for multiple brandIds and channelIds for now
         * So I just hard code it as [0] here
         **/
            $brandId = $firstAdType->brandId;
            $channelId = $firstAdType->channelId;

        /**
         * Get the user with given email, or create one
         * userController@getOrCreateUser in userService
         **/
            $usersBeaconSlug = 'users';
            $userServiceResourcePath = "users/get-or-create";
            $userServicePostDataArray = [
            'email' => $email,
            'name' => $name
            ];

            $user = $this->beaconService->createResourceByBeaconSlug(
                $usersBeaconSlug,
                $brandId,
                $channelId,
                $userServiceResourcePath,
                $userServicePostDataArray,
                false
            );

        if (empty($user)) {
            Rollbar::log(Level::ERROR, "We weren\'t able to create this user with {$email} and {$name}.");
            return response()->json('We weren\'t able to create this user.', 500);
        }

        /**
         * create a package with packages_controller@create_package in adService.
         **/
        $adBeaconSlug = 'ads';
        $adServiceResourcePath = "brands/{$brandId}/channels/{$channelId}/packages";
        $adTypesInPackage = json_encode($adTypesInPackage);

        $adServicePostDataArray = [
            'brandId' => $brandId,
            'channelId' => $channelId,
            'adTypesInPackage' => $adTypesInPackage,
            'description' => "Customer pakage with pipedriveId: {$pipedriveId}",
            'isDisplayed' => false,
            'name' => "Customer pakage with pipedriveId: {$pipedriveId}",
            'price' => $price,
            'packageImage' => ''
        ];

        $package = $this->beaconService->createResourceByBeaconSlug(
            $adBeaconSlug,
            $brandId,
            $channelId,
            $adServiceResourcePath,
            $adServicePostDataArray,
            false
        );

        if (empty($package)) {
            Rollbar::log(Level::ERROR, "We weren\'t able to create this package with {$adTypesInPackage}, {$price} and {$pipedriveId}.");
            return response()->json('We weren\'t able to create this package.', 500);
        }

        /**
         * create an order in adService
         **/
        $costOfPackage = $package->price;
        $packageId = $package->id;
        $userId = $user->id;
        $company = $user->name;

        $adsBeaconSlug = 'ads';
        $adServiceResourcePath = 'orders';
        $adServicePostDataArray = [
            'amount' => $costOfPackage,
            'brandId' => $brandId,
            'channelId' => $channelId,
            'company' => $company,
            'package_id' => $packageId,
            'platformUserId' => $userId,
            'originalPurchasePrice' => $costOfPackage,
            'stripe_id' => $pipedriveId,
            'revenueShare' => $revenueShare
        ];

        $order = $this->beaconService->createResourceByBeaconSlug($adsBeaconSlug, $brandId, $channelId, $adServiceResourcePath, $adServicePostDataArray, false);

        if (empty($order)) {
            Rollbar::log(Level::ERROR, "We weren\'t able to save your order with {$costOfPackage}, {$packageId}, {$userId} and {$company}.");
            return response()->json('We weren\'t able to save your order.', 500);
        }

        return response()->json($order);
    }

    public function updateAd(Channel $channel, int $adId, Request $request): JsonResponse
    {
        $adServicePostDataArray = $this->adService->getAdRequestFormattedForMultipartPost($channel, $request);
        $beaconSlug = 'ads';
        $restfulResourcePath = "ads/{$adId}";

        $adUpdated = $this->beaconService->createResourceByBeaconSlug(
            $beaconSlug,
            $channel->getBrandId(),
            $channel->getId(),
            $restfulResourcePath,
            $adServicePostDataArray,
            true
        );

        if (empty($adUpdated)) {
            return response()->json('We were not able to update this ad', 500);
        }

        return response()->json($adUpdated, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function deleteAd(int $adId): JsonResponse
    {
        $restfulResourcePath = "ads/{$adId}";

        $wasAdDeleted = $this->beaconService->deleteResourceFromService('ads', $restfulResourcePath);

        if ($wasAdDeleted === false) {
            return response()->json($wasAdDeleted, 500);
        }

        return response()->json($wasAdDeleted, 200);
    }

    public function getAdById(Request $request, int $adId): JsonResponse
    {
        /**
         * Checking to see if we need additional information about the ad credit
         */
        $beaconSlug = 'ads';
        $creditQueryParamater = filter_var($request->input('resolveCredit', false), FILTER_VALIDATE_BOOLEAN);
        $mjmlQueryParameter = $request->input('mjml', 'false');
        $restfulResourcePath = "promotions/{$adId}/?mjml={$mjmlQueryParameter}";

        $ad = $this->beaconService->getAdResourceByBeaconSlug(
            $beaconSlug,
            $restfulResourcePath
        );

        if (empty($ad)) {
            return response()->json("Ad with adId ${adId} can't be found.", 404);
        }

        if ($creditQueryParamater) {
            $promotionCredit = $this->getPromotionCreditInfoByPromotionId($adId);

            $promotionCreditData = $promotionCredit->getData();

            $adWithCredit = [
                "ad" => $ad,
                "promotionCredit" => $promotionCreditData
            ];

            return response()->json($adWithCredit, 200, [], JSON_UNESCAPED_SLASHES);
        }

        return response()->json($ad, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
