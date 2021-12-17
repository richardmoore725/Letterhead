<?php

use App\Tests\TestCase;
use App\DTOs\ChannelDto;
use App\DTOs\UserDto;
use App\Http\Services\adServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Jobs\EmailPromoterPromoStatusChangedJob;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\PromotionCredit;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class EmailPromoterPromoStatusChangedJobTest extends TestCase
{
    private $channel;
    private $userService;

    public function setUp(): void
    {
        $this->promotion = $this->createMock(Promotion::class);
        $this->promotionCredit = $this->createMock(PromotionCredit::class);

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

        $this->adService = $this->createMock(adServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);

        $this->job = new EmailPromoterPromoStatusChangedJob($this->promotion);
    }

    public function testCannotSendBrandAdministratorsEmail__emptyChannel()
    {
        $dto = new UserDto(new \stdClass);
        $dto->created_at = '2020-11-03';
        $dto->email = 'support@whereby.us';
        $dto->id = 1;
        $dto->name = 'charles';
        $dto->surname = 'villard';
        $user = new User($dto);

        $userId = $user->getId();

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with($this->promotion->getId())
            ->willReturn(null);

        $actualResults = $this->job->handle($this->adService, $this->channelService, $this->userService, $this->queue);
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

        $userId = $user->getId();

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with($this->promotion->getId())
            ->willReturn($this->channel);

        $this->adService
            ->expects($this->once())
            ->method('getPromotionCreditByPromotionId')
            ->with($this->promotion->getId())
            ->willReturn($this->promotionCredit);

        $this->userService
            ->expects($this->once())
            ->method('getUserById')
            ->with($this->promotionCredit->getUserId())
            ->willReturn($user);

        $actualResults = $this->job->handle($this->adService, $this->channelService, $this->userService, $this->queue);
        $this->assertEmpty($actualResults);
    }
}