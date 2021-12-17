<?php

namespace App\Tests;

use App\Mail\MailTemplateGenerator;
use App\Mail\TransactionalEmailMailable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class TransactionalEmailMailableTest extends TestCase
{
    public function testCanBuildTemplate_returnsMailable()
    {
        $callToaction = 'Click here';
        $callToActionUrl = 'https://google.com';
        $copy = 'Hello';
        $from = 'test@whereby.us';
        $heading = 'I am a heading';
        $subject = 'And I am a subject';

        $mailable = new TransactionalEmailMailable(
            $callToaction,
            $callToActionUrl,
            $copy,
            $from,
            $heading,
            $subject
        );

        $this->assertInstanceOf(Mailable::class, $mailable);
    }
}
