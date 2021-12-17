<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyBrandMiddleware;
use App\Http\Middleware\VerifyConfigurationMiddleware;
use App\Models\Brand;
use App\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Services\BrandServiceInterface;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class BrandConfigurationController extends Controller
{
    private $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    public function connectBrandAccountToStripe(Request $request)
    {
        $code = $request->input('code');
        $state = $request->input('state');

        try {
            $decodedState = urldecode($state);
            $stateContent = json_decode($decodedState);
            $brandId = $stateContent->brandId;
            $redirectUri = $stateContent->redirectUri;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            die();
        }

        $updateResult = $this->brandService->updateBrandConfigurationWithStripeDto($code, $brandId);

        $statusCode = $updateResult ? 201 : 500;
        $redirectUriWithStatus = "{$redirectUri}/?status={$statusCode}";

        return redirect($redirectUriWithStatus);
    }

    /**
     * This method simply sets the value of a BrandConfiguration and returns either true or false with
     * appropriate HTTP status codes. Middleware assures us that the Brand and that Configuration exists.
     *
     * @uses VerifyBrandMiddleware
     * @uses VerifyConfigurationMiddleware
     * @return JsonResponse
     */
    public function setBrandConfiguration(Brand $brand, Configuration $configuration, Request $request): JsonResponse
    {
        $brandConfigurationValue = $request->input('brandConfigurationValue');

        if (empty($brandConfigurationValue)) {
            return response()->json('The brandConfigurationValue is required', 400);
        }

        $hasBrandConfigurationBeenSet = $this->brandService
            ->setBrandConfiguration($brandConfigurationValue, $brand->getId(), $configuration->getId());

        if (!$hasBrandConfigurationBeenSet) {
            return response()->json("Woops. Something went wrong on our end.", 500);
        }

        return response()->json(true, 200);
    }

    public function deauthorizedFromStripe(Request $request): JsonResponse
    {
        $account = $request->input('account');



        $updatedAccount = $this->brandService->updateBrandConfigurationByBrandConfigurationValue($account, '');

        if (!$updatedAccount) {
            return response()->json("Sorry we were unable to disconnect you from Stripe.", 500);
        }

        return response()->json("You are disconnected from Stripe.", 200);
    }
}
