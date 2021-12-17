<?php

namespace App\Http\Controllers;

use App\Http\Services\TransactionalEmailServiceInterface;
use App\Http\Services\EmailServiceInterface;
use App\Models\TransactionalEmail;
use App\Models\Channel;
use App\Models\PlatformEvent;
use App\Jobs\ShouldTransactionalEmailBeSentJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionalEmailController extends Controller
{
    private $transactionalEmailService;

    public function __construct(
        TransactionalEmailServiceInterface $transactionalEmailService
    ) {
        $this->transactionalEmailService = $transactionalEmailService;
    }

    public function createTransactionalEmail(
        int $brandId,
        int $channelId,
        string $description,
        int $emailId,
        int $eventId,
        bool $isActive,
        string $name
    ): JsonResponse {
        $transactionalEmailToCreate = new TransactionalEmail();
        $transactionalEmailToCreate->setBrandId($brandId);
        $transactionalEmailToCreate->setChannelId($channelId);
        $transactionalEmailToCreate->setDescription($description);
        $transactionalEmailToCreate->setEmailId($emailId);
        $transactionalEmailToCreate->setEventId($eventId);
        $transactionalEmailToCreate->setIsActive($isActive);
        $transactionalEmailToCreate->setName($name);

        $newlyCreatedTransactionalEmail = $this->transactionalEmailService->createTransactionalEmail($transactionalEmailToCreate);

        if (empty($newlyCreatedTransactionalEmail)) {
            return response()->json('We were not able to create this transactional email', 500);
        }

        return response()->json($newlyCreatedTransactionalEmail->convertToArray());
    }

    public function deleteTransactionalEmail(TransactionalEmail $transactionalEmail): JsonResponse
    {
        $wasTransactionalEmailDeleted = $this->transactionalEmailService->deleteTransactionalEmail($transactionalEmail);

        if ($wasTransactionalEmailDeleted) {
            return response()->json('We have deleted this transactional email.', 200);
        }

        return response()->json('We were not able to delete this transactional email.', 500);
    }

    public function updateTransactionalEmail(
        TransactionalEmail $transactionalEmail,
        int $brandId,
        int $channelId,
        string $description,
        int $emailId,
        int $eventId,
        bool $isActive,
        string $name
    ): JsonResponse {
        $transactionalEmail->setBrandId($brandId);
        $transactionalEmail->setChannelId($channelId);
        $transactionalEmail->setDescription($description);
        $transactionalEmail->setEmailId($emailId);
        $transactionalEmail->setEventId($eventId);
        $transactionalEmail->setIsActive($isActive);
        $transactionalEmail->setName($name);

        $updatedTransactionalEmail = $this->transactionalEmailService->updateTransactionalEmail($transactionalEmail);

        if (empty($updatedTransactionalEmail)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedTransactionalEmail->convertToArray());
    }

    public function getTransactionalEmailById(TransactionalEmail $transactionalEmail): JsonResponse
    {
        return response()->json($transactionalEmail->convertToArray());
    }

    public function getTransactionalEmailsByChannel(Channel $channel): JsonResponse
    {
        $channelId = $channel->getId();

        $transactionalEmails = $this->transactionalEmailService->getTransactionalEmailsByChannel($channelId);

        if (empty($transactionalEmails)) {
            return response()->json('Something went wrong', 404);
        }

        return response()->json($transactionalEmails);
    }
}
