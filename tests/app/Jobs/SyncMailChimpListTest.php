<?php

namespace App\Tests;

use App\Http\Services\BrandServiceInterface;
use App\Http\Services\MailChimpFacadeInterface;
use App\Models\Channel;
use App\Models\Configuration;
use App\Models\MailChimpList;
use App\DTOs\MailChimpListDto;
use App\Collections\ChannelConfigurationCollection;
use DrewM\MailChimp\MailChimp;
use Illuminate\Contracts\Queue\Queue;
use App\Jobs\SyncMailChimpListData;
use App\Tests\Exception;

class SyncMailChimpListDataTest extends TestCase
{
    private $channel;
    private $originalMailChimpSelectedListId;
    private $brandService;
    private $mailChimpFacade;
    private $job;
    private $queue;

    public function setUp() : void
    {
        $this->channel = $this->createMock(Channel::class);
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->mailChimpFacade = $this->createMock(MailChimpFacadeInterface::class);
        $this->queue = $this->createMock(Queue::class);

        $this->job = new SyncMailChimpListData($this->channel);
    }

    public function testCannotSyncMailChimpListData_avoidPotentialLoop_returnsNothing()
    {
        $this->channel->expects($this->once())
            ->method('getMailChimp')
            ->willReturn($this->mailChimpFacade);

        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('1234567abc');

        $actualResults = $this->job->handle(
            $this->brandService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }

    public function testCannotSyncMailChimpListData_emptyList_returnsNothing()
    {
        $this->channel->expects($this->once())
            ->method('getMailChimp')
            ->willReturn($this->mailChimpFacade);

        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('abc1234567');

        $this->mailChimpFacade
            ->expects($this->once())
            ->method('getListById')
            ->willReturn(null);

        $apiKeyConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(0))
            ->method('getConfigurationBySlug')
            ->with('mcApiKey')
            ->willReturn($apiKeyConfiguration);

        $mailChimpListConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(1))
            ->method('getConfigurationBySlug')
            ->with('mcSelectedEmailListId')
            ->willReturn($mailChimpListConfiguration);

        $integrationEnabledConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(2))
            ->method('getConfigurationBySlug')
            ->with('mcIntegration')
            ->willReturn($integrationEnabledConfiguration);

        $apiKeyConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $mailChimpListConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $integrationEnabledConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(3);

        $actualResults = $this->job->handle(
            $this->brandService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }

    public function testCannotSyncMailChimpListData_throwException()
    {
        $this->channel->expects($this->once())
            ->method('getMailChimp')
            ->willReturn($this->mailChimpFacade);

        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('abc1234567');

        $mailChimp = $this->createMock(MailChimp::class);

        $this->mailChimpFacade
            ->expects($this->once())
            ->method('getListById')
            ->will($this->throwException(new \Exception()));

        $actualResults = $this->job->handle(
            $this->brandService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }

    public function testCanSyncMailChimpListData_returnsNothing()
    {
        $this->channel->expects($this->once())
            ->method('getMailChimp')
            ->willReturn($this->mailChimpFacade);

        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('abc1234567');

        $mailChimp = $this->createMock(MailChimp::class);

        $dto = new MailChimpListDto();
        $dto->clickRate = 0.4;
        $dto->id = 1;
        $dto->name = 'mailChimpList';
        $dto->openRate = 0.6;
        $dto->totalSubscribers = 100;
        $mailChimpList = new MailChimpList($dto);

        $this->mailChimpFacade
            ->expects($this->once())
            ->method('getListById')
            ->willReturn($mailChimpList);

        $clickthroughRateConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(0))
            ->method('getConfigurationBySlug')
            ->with('clickthroughRate')
            ->willReturn($clickthroughRateConfiguration);

        $openRateConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(1))
            ->method('getConfigurationBySlug')
            ->with('openRate')
            ->willReturn($openRateConfiguration);

        $totalSubscribersConfiguration = $this->createMock(Configuration::class);

        $this->brandService
            ->expects($this->at(2))
            ->method('getConfigurationBySlug')
            ->with('totalSubscribers')
            ->willReturn($totalSubscribersConfiguration);

        $clickthroughRateConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $openRateConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $totalSubscribersConfiguration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(3);

        $this->queue
        ->expects($this->once())
        ->method('laterOn');

        $actualResults = $this->job->handle(
            $this->brandService,
            $this->queue
        );

        $this->assertEmpty($actualResults);
    }
}
