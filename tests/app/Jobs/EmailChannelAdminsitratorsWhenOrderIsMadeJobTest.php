<?php

namespace App\Tests;

use App\Collections\UserCollection;
use App\Models\Channel;
use App\Models\User;
use App\DTOs\UserDto;
use App\Http\Services\UserServiceInterface;
use App\Jobs\EmailChannelAdministratorsWhenOrderIsMadeJob;
use Illuminate\Contracts\Queue\Queue;

class EmailChannelAdminsitratorsWhenOrderIsMadeJobTest extends TestCase
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
    private $job;
    private $userService;
    private $queue;

    public function setUp() : void
    {
        $this->newsletter = $this->createMock(Channel::class);
        $this->friendlyPublicationDate = 'Nov. 3, 2020';
        $this->orderId = 109283;
        $this->channelId = 1;
        $this->channelName = 'Black Bitter Tea Sippin\'';
        $this->packageName = 'Banner Pack';
        $this->originalPackagePrice = 100;
        $this->discountValue = 50;
        $this->finalPackagePrice = 50;
        $this->userName = 'cdvillard';
        $this->fromEmail = 'support@whereby.us';

        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->queue = $this->createMock(Queue::class);

        $this->job = new EmailChannelAdministratorsWhenOrderIsMadeJob(
            $this->friendlyPublicationDate,
            $this->fromEmail,
            $this->orderId,
            $this->channelId,
            $this->channelName,
            $this->originalPackagePrice,
            $this->discountValue,
            $this->finalPackagePrice,
            $this->userName,
            $this->packageName,
        );
    }


    public function testCannotEmailChannelAdminsitratorsWhenOrderIsMade__returnsNothing() {
        $dto = new UserDto(new \stdClass);
        $dto->created_at = '2020-11-03';
        $dto->email = 'support@whereby.us';
        $dto->id = 1;
        $dto->name = 'charles';
        $dto->surname = 'villard';
        $user = new User($dto);

        $users = new userCollection([$user, ]);


        $this->userService
            ->expects($this->once())
            ->method('getBrandAdministrators')
            ->willReturn($users);


        $actualResults = $this->job->handle(
            $this->userService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }
}