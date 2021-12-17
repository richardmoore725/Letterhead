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

class EmailChannelAdministratorsPromoIsRescheduledJob extends Job
{
    private $friendlyPublicationDate;
    private $fromEmail;
    private $promotionId;
    private $userName;
    private $newsletter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $friendlyPublicationDate,
        string $fromEmail,
        int $promotionId,
        string $userName,
        Channel $newsletter
    ) {
        $this->friendlyPublicationDate = $friendlyPublicationDate;
        $this->fromEmail = $fromEmail;
        $this->promotionId = $promotionId;
        $this->userName = $userName;
        $this->newsletter = $newsletter;
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
        $brandAdministrators = $userService->getBrandAdministrators($this->newsletter->getBrandId());
        $newsletterName = $this->newsletter->getTitle();

        $promotionUrl = env('SERVICE_ARAGORN_URL');

        $callToAction = 'Check promotion';
        $callToActionUrl = "{$promotionUrl}/promotions/{$this->promotionId}";

        $heading = "Promotion {$this->promotionId} in {$newsletterName} has been rescheduled";

        $administratorEmailsArray = array_map(
            function ($brandAdministrator) use ($queue, $newsletterName, $callToAction, $callToActionUrl, $heading) {
                $administratorEmail = $brandAdministrator->getEmail();
                $administratorName = $brandAdministrator->getName();

                $copy = "
                    <p>
                        Hi {$administratorName},
                    </p>
        
                    <p>
                        We are just confirming {$this->userName} rescheduled promotion {$this->promotionId} in {$newsletterName}
                        to <b>{$this->friendlyPublicationDate}</b>.
                    </p>
        
                    <table>
                        <tbody>
                            <tr>
                                <td style='font-weight: bold;'>#</td>
                                <td><a href='{$callToActionUrl}'>{$this->promotionId}</a></td>
                            </tr>
        
                            <tr>
                                <td style='font-weight: bold;'>Newsletter</td>
                                <td>{$newsletterName}</td>
                            </tr>
                            
                            <tr>
                                <td style='font-weight:bold;'>Scheduled</td>
                                <td>{$this->friendlyPublicationDate}</td>
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
