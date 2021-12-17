<?php

namespace App\Listeners;

use App\Events\OrderPurchasedEvent;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\SendEmailJob;
use App\Models\Email;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class SendOrderNotificationToSales
{
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Handle the event.
     *
     * @param  OrderPurchasedEvent  $event
     * @return void
     */
    public function handle(OrderPurchasedEvent $event)
    {
        $userName = $event->passport->getName() ? $event->passport->getName() : $event->company;
        $channelName = $event->channel->getTitle();
        $date = $event->date;
        $orderId = $event->orderId;
        $packageName = $event->packageName;
        $originalPackagePrice = $event->originalPackagePrice / 100;
        $discountValue = $event->discountValue;
        $finalPackagePrice = $event->finalPackagePrice / 100;

        $callToAction = "View order #{$orderId}";

        $letterHeadBaseUrl = env('SERVICE_ARAGORN_URL', 'https://app.tryletterhead.com');
        $callToActionUrl = "{$letterHeadBaseUrl}/orders/{$orderId}";

        $toEmail = env('MAIL_SALES_ADDRESS', 'new-orders@tryletterhead.com');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@tryletterhead.com');
        $heading = "A new order #{$orderId} was just placed in {$channelName}";

        $copy = "
        <p>
            Good job, sales team! {$userName} just placed a new order in <b>{$channelName}</b>. Here is a copy of the receipt,
            which you can view any time on Letterhead.
        </p>

        <div
        class='table'
        style='
          font-family: Avenir, Helvetica, sans-serif;
          box-sizing: border-box;
        '
        >
        <table
        style='
            font-family: Avenir, Helvetica, sans-serif;
            box-sizing: border-box;
            margin: 30px auto;
            width: 100%;
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
        '
        >
            <tbody
            style='
                font-family: Avenir, Helvetica, sans-serif;
                box-sizing: border-box;
                '
            >
                <tr>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                  box-sizing: border-box;
                                  color: #202a33;
                                  font-size: 15px;
                                  font-weight: bold;
                                  line-height: 18px;
                                  padding: 10px 0;'>
                    Date:</td>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                  box-sizing: border-box;
                                  color: #202a33;
                                  font-size: 15px;
                                  line-height: 18px;
                                  padding: 10px 0;'>
                    {$date}
                    </td>
                </tr>
                <tr>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                  box-sizing: border-box;
                                  color: #202a33;
                                  font-size: 15px;
                                  font-weight: bold;
                                  line-height: 18px;
                                  padding: 10px 0;'>
                    Order ID:</td>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                  box-sizing: border-box;
                                  color: #202a33;
                                  font-size: 15px;
                                  line-height: 18px;
                                  padding: 10px 0;'>
                    {$orderId}
                    </td>
                </tr>
                <tr>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                font-weight: bold;
                                line-height: 18px;
                                padding: 10px 0;'>
                    Newsletter:</td>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                line-height: 18px;
                                padding: 10px 0;'>
                    {$channelName}
                    </td>
                </tr>
                <tr>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                font-weight: bold;
                                line-height: 18px;
                                padding: 10px 0;'>
                    Package:</td>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                line-height: 18px;
                                padding: 10px 0;'>
                    {$packageName}
                    </td>
                </tr>
                <tr>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                font-weight: bold;
                                line-height: 18px;
                                padding: 10px 0;'>
                    Sub-total:</td>
                    <td
                        style='
                            font-family: Avenir, Helvetica, sans-serif;
                                box-sizing: border-box;
                                color: #202a33;
                                font-size: 15px;
                                line-height: 18px;
                                padding: 10px 0;'>
                    <span>&#36;</span>{$originalPackagePrice}
                    </td>
                </tr>
                <tr>
                <td
                    style='
                        font-family: Avenir, Helvetica, sans-serif;
                            box-sizing: border-box;
                            color: #202a33;
                            font-size: 15px;
                            font-weight: bold;
                            line-height: 18px;
                            padding: 10px 0;'>
                Discount applied:</td>
                <td
                    style='
                        font-family: Avenir, Helvetica, sans-serif;
                            box-sizing: border-box;
                            color: #202a33;
                            font-size: 15px;
                            line-height: 18px;
                            padding: 10px 0;'>
                {$discountValue}% OFF
                </td>
            </tr>
            <tr>
            <td
                style='
                    font-family: Avenir, Helvetica, sans-serif;
                        box-sizing: border-box;
                        color: #202a33;
                        font-size: 15px;
                        font-weight: bold;
                        line-height: 18px;
                        padding: 10px 0;'>
            Total:</td>
            <td
                style='
                    font-family: Avenir, Helvetica, sans-serif;
                        box-sizing: border-box;
                        color: #202a33;
                        font-size: 15px;
                        line-height: 18px;
                        padding: 10px 0;'>
            <span>&#36;</span>{$finalPackagePrice}
            </td>
        </tr>
            </tbody>
        </table>
    </div>
        ";

        $email = new Email();
        $email->setContent($copy);
        $email->setFromEmail($fromEmail);
        $email->setSubject($heading);

        $emailJob = new SendEmailJob($callToAction, $callToActionUrl, $email, $toEmail);

        $this->queue->pushOn('send_email', $emailJob);
    }
}
