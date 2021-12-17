<?php

namespace App\Http\Controllers;

use App\Http\Services\MailChimpFacadeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailChimpController extends Controller
{
    public function getListById(MailChimpFacadeInterface $mailChimp, string $id): JsonResponse
    {
        $list = $mailChimp->getListById($id);

        return !empty($list)
          ? response()->json($list->convertToArray())
          : response()->json("This list doesn't exist.", 404);
    }

    public function getLists(MailChimpFacadeInterface $mailChimp): JsonResponse
    {
        return response()->json($mailChimp->getLists()->getPublicArray());
    }
}
