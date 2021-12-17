<?php

namespace App\Tests;

use App\Models\Email;
use App\Http\Services\MailServiceInterface;
use App\Jobs\SendEmailJob;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class SendEmailJobTest extends TestCase
{
    private $email;
    private $mailService;

    public function setUp() : void
    {
        $this->email = $this->createMock(Email::class);
        $this->mailService = $this->createMock(MailServiceInterface::class);

        $this->job = new SendEmailJob('Click here', 'https://whereby.us', $this->email, 'to@test.com');
    }

    public function testCanSendEmail_returnsNothing()
    {
        $this->email->expects($this->once())
            ->method('getContent')
            ->willReturn('wee');

        $this->email->expects($this->once())
            ->method('getFromEmail')
            ->willReturn('wee@woo.com');

        $this->email->expects($this->once())
            ->method('getSubject')
            ->willReturn('Hummmm');

        $this->mailService
            ->expects($this->once())
            ->method('sendEmail')
            ->with('Click here', 'https://whereby.us', 'wee', 'wee@woo.com', 'Hummmm', 'to@test.com');

        $actualResults = $this->job->handle($this->mailService);

        $this->assertEmpty($actualResults);
    }
}
