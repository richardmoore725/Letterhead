<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionalEmailMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $callToAction;
    private $callToActionUrl;
    private $copy;
    private $heading;
    private $fromEmail;
    private $emailSubject;

    /**
     * TransactionalEmailMailable constructor.
     * @param string $callToAction
     * @param string $callToActionUrl
     * @param string $copy
     * @param string $heading
     * @param string $from
     * @param string $subject
     */
    public function __construct(string $callToAction, string $callToActionUrl, string $copy, string $from, string $heading, string $subject)
    {
        $this->callToAction = $callToAction;
        $this->callToActionUrl = $callToActionUrl;
        $this->copy = $copy;
        $this->heading = $heading;
        $this->fromEmail = $from;
        $this->emailSubject = $subject;
    }

    /**
     * Generates the full email template from the information we pass and returns it, which can then be
     * mailed.
     *
     * @return $this
     */
    public function build()
    {
        $templateValues = [
            'callToAction' => $this->callToAction,
            'callToActionUrl' => $this->callToActionUrl,
            'copy' => $this->copy,
            'heading' => $this->heading
        ];

        /**
         * Renders the 'email' template.
         * @uses resources/views/email.blade.php
         */
        return $this->from($this->fromEmail, 'Letterhead')
                    ->subject($this->emailSubject)
                    ->view('email')
                    ->with($templateValues);
    }
}
