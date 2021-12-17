<?php

namespace App\Http\Controllers;

use App\Http\Services\EmailServiceInterface;
use App\Models\Email;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    private $EmailService;

    public function __construct(EmailServiceInterface $emailService)
    {
        $this->emailService = $emailService;
    }

    public function createEmail(
        int $brandId,
        int $channelId,
        string $content,
        string $description,
        string $fromEmail,
        string $fromName,
        string $name,
        string $subject
    ): JsonResponse {
        $emailToCreate = new Email();
        $emailToCreate->setBrandId($brandId);
        $emailToCreate->setChannelId($channelId);
        $emailToCreate->setContent($content);
        $emailToCreate->setDescription($description);
        $emailToCreate->setFromEmail($fromEmail);
        $emailToCreate->setFromName($fromName);
        $emailToCreate->setName($name);
        $emailToCreate->setSubject($subject);

        $newlyCreatedEmail = $this->emailService->createEmail($emailToCreate);

        if (empty($newlyCreatedEmail)) {
            return response()->json('We were not able to create this email', 500);
        }

        return response()->json($newlyCreatedEmail->convertToArray());
    }

    public function deleteEmail(Email $email): JsonResponse
    {
        $wasEmailDeleted = $this->emailService->deleteEmail($email);

        if ($wasEmailDeleted) {
            return response()->json('We have deleted this email.', 200);
        }

        return response()->json('We were not able to delete this email.', 500);
    }

    public function updateEmail(
        Email $email,
        int $brandId,
        int $channelId,
        string $content,
        string $description,
        string $fromEmail,
        string $fromName,
        string $name,
        string $subject
    ): JsonResponse {
        $email->setBrandId($brandId);
        $email->setChannelId($channelId);
        $email->setContent($content);
        $email->setDescription($description);
        $email->setFromEmail($fromEmail);
        $email->setFromName($fromName);
        $email->setName($name);
        $email->setSubject($subject);

        $updatedEmail = $this->emailService->updateEmail($email);

        if (empty($updatedEmail)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedEmail->convertToArray());
    }

    public function getEmailById(Email $email): JsonResponse
    {
        return response()->json($email->convertToArray());
    }

    public function getEmailsByChannel(channel $channel): JsonResponse
    {
        $channelId = $channel->getId();

        $emails = $this->emailService->getEmailsByChannel($channelId);

        if (empty($emails)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($emails);
    }
}
