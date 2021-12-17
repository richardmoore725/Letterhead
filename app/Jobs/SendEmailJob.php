<?php

namespace App\Jobs;

use App\Models\Email;
use App\Http\Services\MailServiceInterface;

class SendEmailJob extends Job
{
    private $callToAction;
    private $callToActionUrl;
    private $toEmail;
    private $email;
    private $user;

    public function __construct(string $callToAction, string $callToActionUrl, Email $email, string $toEmail)
    {
        $this->callToAction = $callToAction;
        $this->callToActionUrl = $callToActionUrl;
        $this->email = $email;
        $this->toEmail = $toEmail;
    }

    public function handle(MailServiceInterface $mailService): void
    {
        $mailService->sendEmail(
            $this->callToAction,
            $this->callToActionUrl,
            $this->email->getContent(),
            $this->email->getFromEmail(),
            $this->email->getSubject(),
            $this->toEmail
        );
    }
}
