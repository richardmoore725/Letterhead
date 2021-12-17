<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\DiscountCode;
use App\Http\Services\DiscountCodeServiceInterface;
use Illuminate\Http\JsonResponse;

class DiscountCodeController extends Controller
{
    private $discountCodeService;

    public function __construct(DiscountCodeServiceInterface $discountCodeService)
    {
        $this->discountCodeService = $discountCodeService;
    }

    public function checkIfCodeWasAlreadyDefined(string $discountCode): JsonResponse
    {
        $wasThisCodeAlreadyDefined = $this->discountCodeService->checkIfCodeWasAlreadyDefined($discountCode);

        return response()->json($wasThisCodeAlreadyDefined);
    }

    public function createDiscountCode(
        int $channelId,
        string $discountCode,
        int $discountValue,
        string $displayName,
        bool $isActive
    ): ?JsonResponse {

        $wasThisCodeAlreadyDefined = $this->discountCodeService->checkIfCodeWasAlreadyDefined($discountCode);

        if ($wasThisCodeAlreadyDefined) {
            return response()->json('It seems this code has already been defined. Please create a new code with a unique string.', 400);
        }

        $discountCodeToCreate = new DiscountCode();

        $discountCodeToCreate->setChannelId($channelId);
        $discountCodeToCreate->setDiscountCode($discountCode);
        $discountCodeToCreate->setDiscountValue($discountValue);
        $discountCodeToCreate->setDisplayName($displayName);
        $discountCodeToCreate->setIsActive($isActive);

        $discountCodeCreated = $this->discountCodeService->createDiscountCode($discountCodeToCreate);

        if (empty($discountCodeCreated)) {
            return response()->json('We were not able to create this discount code', 500);
        }

        return response()->json($discountCodeCreated->convertToArray(), 201);
    }

    public function getDiscountCodes(int $channelId): JsonResponse
    {
        $discountCodes = $this->discountCodeService->getDiscountCodesByChannelId($channelId);

        if (empty($discountCodes)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($discountCodes->getPublicArrays());
    }

    public function getDiscountCode(Channel $channel, DiscountCode $discountCodeObject): JsonResponse
    {
        $channelId = $channel->getId();
        $discountCodeChannelId = $discountCodeObject->getChannelId();

        if ($channelId !== $discountCodeChannelId) {
            return response()->json('This discount code is unavailable.', 404);
        }

        return response()->json($discountCodeObject->convertToArray());
    }

    public function deleteDiscountCodeById(int $discountCodeId): JsonResponse
    {
        $discountCodeWasDeleted = $this->discountCodeService->deleteDiscountCode($discountCodeId);

        return response()->json($discountCodeWasDeleted);
    }

    public function updateDiscountCode(
        DiscountCode $discountCodeObject,
        bool $isActive
    ): JsonResponse {
        $discountCodeObject->setIsActive($isActive);

        $updatedDiscountCode = $this->discountCodeService->updateDiscountCode($discountCodeObject);

        if (empty($updatedDiscountCode)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedDiscountCode->convertToArray());
    }
}
