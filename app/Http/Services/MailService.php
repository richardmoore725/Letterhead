<?php

namespace App\Http\Services;

use App\Mail\SendOrderReceipt;
use App\Mail\TransactionalEmailMailable;
use Illuminate\Contracts\Mail\Mailer;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class MailService implements MailServiceInterface
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
        string $callToAction,
        string $callToActionUrl,
        string $copy,
        string $fromEmail,
        string $subject,
        string $toEmail
    ) {
        $email = new TransactionalEmailMailable($callToAction, $callToActionUrl, $copy, $fromEmail, $subject, $subject);
        return $this->mailer->to($toEmail)->send($email);
    }

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
    ) {
        try {
            $receiptEmail = new SendOrderReceipt(
                $brandName,
                $customerName,
                $channelName,
                $date,
                $orderId,
                $packageName,
                $price,
                $redirectUrl
            );

            $this->mailer->to($toEmail)->send($receiptEmail);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage(), $e->getTrace());
            return null;
        }
    }
}
