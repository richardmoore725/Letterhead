<?php

namespace App\Tests;

use App\Http\Response;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\LetterServiceInterface;
use App\Jobs\SendLetterThroughEmailServiceProviderJob;
use App\Models\Channel;
use App\Models\Email;
use App\Http\Services\MailServiceInterface;
use App\Jobs\SendEmailJob;
use App\Models\Letter;

class SendLetterThroughEmailServiceProviderJobTest extends TestCase
{
    private $job;
    private $letter;

    public function setUp(): void
    {
        $this->letter = $this->createMock(Letter::class);
        $this->job = new SendLetterThroughEmailServiceProviderJob($this->letter);
    }

    public function testCanSendLetterThroughEmailServiceProviderJob_returnsNothing()
    {
        $channelService = $this->createMock(ChannelServiceInterface::class);
        $letterService = $this->createMock(LetterServiceInterface::class);
        $channel = $this->createMock(Channel::class);
        $response = $this->createMock(Response::class);

        $this->letter->expects($this->once())->method('getChannelId')->willReturn(5);

        $channelService->expects($this->once())->method('getChannelById')->with(5)->willReturn($channel);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProvider')
            ->willReturn(1);

        $this->letter->expects($this->once())
            ->method('getTitle')
            ->willReturn('I am a title');

        $this->letter->expects($this->once())
            ->method('getEmailTemplate')
            ->willReturn('<p>hi</p>');

        $channel->expects($this->once())
            ->method('getDefaultFromEmailAddress')
            ->willReturn('michael@test.com');

        $channel->expects($this->once())
            ->method('getDefaultEmailFromName')
            ->willReturn('michael');

        $letterService->expects($this->once())
            ->method('send')
            ->with($channel, 1, $this->letter, 'michael@test.com', 'michael', 'I am a title', '<p>hi</p>')
            ->willReturn($response);

        $response->expects($this->once())->method('isError')->willReturn(false);

        $letterService->expects($this->once())->method('markLetterasPublished')->with($this->letter)->willReturn(true);

        $actualResults = $this->job->handle($channelService, $letterService);

        $this->assertEmpty($actualResults);
    }

    public function testCannotSendLetterThroughEmailServiceProviderJob_noChannel()
    {
        $channelService = $this->createMock(ChannelServiceInterface::class);
        $letterService = $this->createMock(LetterServiceInterface::class);

        $this->letter->expects($this->once())->method('getChannelId')->willReturn(5);

        $channelService->expects($this->once())->method('getChannelById')->with(5)->willReturn(null);

        $this->expectException(\Exception::class);

        $this->job->handle($channelService, $letterService);
    }

    public function testCannotSendLetterThroughEmailServiceProviderJob_failedSend()
    {
        $channelService = $this->createMock(ChannelServiceInterface::class);
        $letterService = $this->createMock(LetterServiceInterface::class);
        $channel = $this->createMock(Channel::class);
        $response = $this->createMock(Response::class);

        $this->letter->expects($this->once())->method('getChannelId')->willReturn(5);

        $channelService->expects($this->once())->method('getChannelById')->with(5)->willReturn($channel);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProvider')
            ->willReturn(1);

        $this->letter->expects($this->once())
            ->method('getTitle')
            ->willReturn('I am a title');

        $this->letter->expects($this->once())
            ->method('getEmailTemplate')
            ->willReturn('<p>hi</p>');

        $channel->expects($this->once())
            ->method('getDefaultFromEmailAddress')
            ->willReturn('michael@test.com');

        $channel->expects($this->once())
            ->method('getDefaultEmailFromName')
            ->willReturn('michael');

        $letterService->expects($this->once())
            ->method('send')
            ->with($channel, 1, $this->letter, 'michael@test.com', 'michael', 'I am a title', '<p>hi</p>')
            ->willReturn($response);

        $response->expects($this->once())->method('isError')->willReturn(true);

        $this->expectException(\Exception::class);

        $this->job->handle($channelService, $letterService);
    }
}
