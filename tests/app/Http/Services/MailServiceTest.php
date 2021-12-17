<?php

namespace App\Tests;

use App\Http\Services\MailService;
use App\Mail\MailTemplateGenerator;
use App\Mail\TransactionalEmailMailable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\PendingMail;

class MailServiceTest extends TestCase
{
    private $mailer;
    private $service;
    private $pendingMail;
    private $email;

    public function setUp(): void
    {
        $this->mailer = $this->createMock(Mailer::class);
        $this->pendingMail = $this->createMock(PendingMail::class);
        $this->service = new MailService($this->mailer);
        $this->email = $this->createMock(\App\Models\Email::class);
    }

    public function testCanSendEmail()
    {
        $generator = new TransactionalEmailMailable('click here', 'https://whereby.us', 'copy', 'from@email.com', 'weo', 'weo');

        $this->mailer
        ->expects($this->once())
        ->method('to')
        ->with('to@test.com')
        ->Willreturn($this->pendingMail);

        $this->pendingMail
        ->expects($this->once())
        ->method('send')
        ->with($generator)
        ->Willreturn(null);

        $actualResults = $this->service->sendEmail('click here', 'https://whereby.us', 'copy', 'from@email.com', 'weo', 'to@test.com');

        $this->assertNull($actualResults);
    }
}
