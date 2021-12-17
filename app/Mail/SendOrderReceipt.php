<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOrderReceipt extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $brandName;
    private $customerName;
    private $channelName;
    private $date;
    private $orderId;
    private $packageName;
    private $price;
    private $redirectUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $brandName,
        string $customerName,
        string $channelName,
        string $date,
        int $orderId,
        string $packageName,
        int $price,
        string $redirectUrl
    ) {
        $this->brandName = $brandName;
        $this->customerName = $customerName;
        $this->channelName = $channelName;
        $this->date = $date;
        $this->orderId = $orderId;
        $this->packageName = $packageName;
        $this->price = $price;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @uses order.blade.php
     */
    public function build()
    {
        $subject = "Receipt for your payment to {$this->channelName}";

        $templateValues = [
            'brandName' => $this->brandName,
            'channelName' => $this->channelName,
            'customerName' => $this->customerName,
            'date' => $this->date,
            'packageName' => $this->packageName,
            'price' => $this->price,
            'orderId' => $this->orderId,
            'redirectUrl' => $this->redirectUrl
        ];

        return $this->from('noreply@tryletterhead.com')
                    ->subject($subject)
                    ->view('order')
                    ->with($templateValues);
    }
}
