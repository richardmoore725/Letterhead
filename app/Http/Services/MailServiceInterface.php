<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Mail\PendingMail;

interface MailServiceInterface
{
    public function sendEmail(
        string $callToAction,
        string $callToActionUrl,
        string $copy,
        string $fromEmail,
        string $subject,
        string $toEmail
    );

    public function sendOrderReceipt(
        string $brandName,
        string $customerName,
        string $channelName,
        string $date,
        int $orderId,
        string $packageName,
        int $price,
        string $toEmail,
        string $redirectUrl
    );
}
