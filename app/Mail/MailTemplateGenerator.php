<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTemplateGenerator extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $fromEmail;
    public $subject;
    private $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $fromEmail, string $subject, string $content)
    {
        $this->fromEmail = $fromEmail;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->from($this->fromEmail)
                    ->subject($this->subject)
                    ->view('email')
                    ->with([
                        'content' => $this->content
                    ]);
    }
}
