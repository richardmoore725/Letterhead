<?php

namespace App\Jobs;

use App\Collections\UserCollection;
use App\Models\Channel;
use App\Models\Email;
use App\Models\User;
use App\Http\Services\UserServiceInterface;
use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailChannelAdministratorsWhenOrderIsMadeJob extends Job
{
    private $friendlyPublicationDate;
    private $fromEmail;
    private $orderId;
    private $channelId;
    private $channelName;
    private $originalPackagePrice;
    private $discountValue;
    private $finalPackagePrice;
    private $userName;
    private $packageName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $friendlyPublicationDate,
        string $fromEmail,
        int $orderId,
        int $channelId,
        string $channelName,
        int $originalPackagePrice,
        int $discountValue,
        int $finalPackagePrice,
        string $userName,
        string $packageName
    ) {
        $this->friendlyPublicationDate = $friendlyPublicationDate;
        $this->fromEmail = $fromEmail;
        $this->orderId = $orderId;
        $this->channelId = $channelId;
        $this->channelName = $channelName;
        $this->originalPackagePrice = $originalPackagePrice / 100;
        $this->discountValue = $discountValue;
        $this->finalPackagePrice = $finalPackagePrice / 100;
        $this->userName = $userName;
        $this->packageName = $packageName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        UserServiceInterface $userService,
        Queue $queue
    ): void {
        $brandAdministrators = $userService->getBrandAdministrators($this->channelId);

        $orderUrl = env('SERVICE_ARAGORN_URL', 'https://app.tryletterhead.com');

        $callToAction = "View order #{$this->orderId}";
        $callToActionUrl = "{$orderUrl}/orders/{$this->orderId}";

        $heading = "A new order in {$this->channelName} has been placed";

        $administratorEmailsArray = array_map(
            function ($brandAdministrator) use ($queue, $callToAction, $callToActionUrl, $heading) {
                $administratorEmail = $brandAdministrator->getEmail();
                $administratorName = $brandAdministrator->getName();

                $copy = "
                    <p>
                        Hi {$administratorName},
                    </p>

                    <p>
                        We are just confirming {$this->userName} just ordered a {$this->packageName} package in {$this->channelName}
                        on <b>{$this->friendlyPublicationDate}</b>. Congrats!
                    </p>


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
                                {$this->friendlyPublicationDate}
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
                                {$this->orderId}
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
                                {$this->channelName}
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
                                {$this->packageName}
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
                                <span>&#36;</span>{$this->originalPackagePrice}
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
                                {$this->discountValue}% OFF
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
                                <span>&#36;</span>{$this->finalPackagePrice}
                                </td>
                             </tr>
                        </tbody>
                    </table>
                ";

                $email = new Email();
                $email->setContent($copy);
                $email->setFromEmail($this->fromEmail);
                $email->setSubject($heading);

                $emailJob = new SendEmailJob(
                    $callToAction,
                    $callToActionUrl,
                    $email,
                    $administratorEmail
                );

                $queue->pushOn('send_email', $emailJob);
            },
            $brandAdministrators->toArray()
        );
    }
}
