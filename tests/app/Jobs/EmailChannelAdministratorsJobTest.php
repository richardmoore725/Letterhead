<?php

namespace App\Tests;

use App\Collections\UserCollection;
use App\Models\Channel;
use App\Models\Email;
use App\Models\User;
use App\DTOs\UserDto;
use App\Http\Services\UserServiceInterface;
use App\Jobs\SendEmailJob;
use App\Jobs\EmailChannelAdministratorsJob;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Tests\Exception;

class EmailChannelAdministratorsJobTest extends TestCase
{
    private $friendlyPublicationDate;
    private $fromEmail;
    private $promotionId;
    private $userName;
    private $newsletter;
    private $job;
    private $userService;
    private $queue;

    public function setUp() : void
    {
        $this->newsletter = $this->createMock(Channel::class);
        $this->friendlyPublicationDate = 'Sep 29, 2020';
        $this->promotionId = 1;
        $this->userName = 'jun';
        $this->fromEmail = 'support@whereby.us';

        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->queue = $this->createMock(Queue::class);

        $this->job = new EmailChannelAdministratorsJob(
            $this->friendlyPublicationDate,
            $this->fromEmail,
            $this->promotionId,
            $this->userName,
            $this->newsletter,
        );
    }

    public function testCanEmailChannelAdministrators()
    {
        $dto = new UserDto(new \stdClass);
        $dto->created_at = '2020-09-29';
        $dto->email = 'hello@whereby.us';
        $dto->id = 1;
        $dto->name = 'jun';
        $dto->surname = 'su';
        $user = new User($dto);

        $users = new UserCollection([$user, ]);

        $this->newsletter
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(1);

        $this->userService
            ->expects($this->once())
            ->method('getBrandAdministrators')
            ->willReturn($users);

        $this->newsletter
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn('black bitter coffee');

        $actualResults = $this->job->handle(
            $this->userService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }
}
