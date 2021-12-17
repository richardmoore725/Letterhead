<?php

namespace App\Http\Controllers;

use App\Http\Services\MessageServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Brand;
use App\Models\PassportStamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionMessageController extends Controller
{
    private $userService;
    private $messageService;

    public function __construct(
        UserServiceInterface $userService,
        MessageServiceInterface $messageService
    ) {
        $this->messageService = $messageService;
        $this->userService = $userService;
    }

    /**
     * @authenticated
     * @param Brand $brand
     * @param PassportStamp $passport
     * @param string $message
     * @param int $promotionId
     * @return JsonResponse
     */
    public function createMessage(
        Brand $brand,
        PassportStamp $passport,
        string $message,
        int $promotionId
    ): JsonResponse {
        $isAuthorized = $this->userService->checkWhetherUserCanPerformAction(
            'create',
            'brand',
            $passport,
            $brand->getId()
        );

        $userId = $passport->getId();

        if (!$isAuthorized) {
            return response()->json('You do not have the right privileges on this brand.', 403);
        }

        $response = $this->messageService->createMessage($message, $promotionId, 'promotion', $userId);

        return $response->getJsonResponse();
    }

    /**
     * @authenticated
     * @param Request $request
     * @param Brand $brand
     * @param PassportStamp $passport
     * @return JsonResponse
     */
    public function getMessages(Request $request, Brand $brand, PassportStamp $passport): JsonResponse
    {
        $isAuthorized = $this->userService->checkWhetherUserCanPerformAction(
            'read',
            'brand',
            $passport,
            $brand->getId()
        );

        if (!$isAuthorized) {
            return response()->json('You do not have the right privileges on this brand.', 403);
        }

        $promotionId = $request->get('promotionId');

        if (empty($promotionId)) {
            return response()->json('This requires a promotion id.', 500);
        }

        $messages = $this->messageService->getMessagesByResource($promotionId, 'promotion');

        return response()->json($messages->getPublicArray());
    }
}
