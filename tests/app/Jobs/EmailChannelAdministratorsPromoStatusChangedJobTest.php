<?php

use App\Collections\UserCollection;
use App\Tests\TestCase;
use App\DTOs\ChannelDto;
use App\DTOs\UserDto;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Jobs\EmailChannelAdministratorsPromoStatusChangedJob;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class EmailChannelAdministratorsPromoStatusChangedJobTest extends TestCase
{
    private $channel;
    private $userService;

    public function setUp(): void
    {
        $this->promotion = $this->createMock(Promotion::class);

        $dto = new ChannelDto();
        $dto->id = 2;
        $this->channel = new Channel($dto);
        $this->channel->setAccentColor('#bada55');
        $this->channel->setBrandId(3);
        $this->channel->setChannelSlug('wee');
        $this->channel->setChannelDescription('woo');
        $this->channel->setChannelImage('');
        $this->channel->setTitle('I am a channel');
        $this->channel->setChannelHorizontalLogo('');
        $this->channel->setChannelSquareLogo('');
        $this->channel->setDefaultEmailFromName('jun');
        $this->channel->setDefaultFromEmailAddress('junsu@whereby.us');
        $this->channel->setDefaultFont('serif');
        $this->channel->setEnableChannelAuthoring(false);

        $this->queue = $this->createMock(Queue::class);

        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);

        $this->job = new EmailChannelAdministratorsPromoStatusChangedJob($this->promotion);
    }

    public function testCannotSendBrandAdministratorsEmail__emptyChannel()
    {
        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with($this->promotion->getId())
            ->willReturn(null);

        $actualResults = $this->job->handle($this->channelService, $this->userService, $this->queue);
        $this->assertEmpty($actualResults);
    }

    public function testSendBrandAdministratorsEmail()
    {
        $dto = new UserDto(new \stdClass);
        $dto->created_at = '2020-11-03';
        $dto->email = 'support@whereby.us';
        $dto->id = 1;
        $dto->name = 'charles';
        $dto->surname = 'villard';
        $user = new User($dto);

        $users = new UserCollection([$user, ]);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with($this->promotion->getId())
            ->willReturn($this->channel);

        $this->userService
            ->expects($this->once())
            ->method('getBrandAdministrators')
            ->with($this->channel->getBrandId())
            ->willReturn($users);

        $actualResults = $this->job->handle($this->channelService, $this->userService, $this->queue);
        $this->assertEmpty($actualResults);
    }
}