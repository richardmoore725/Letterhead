<?php

namespace App\Tests;

use App\Collections\ListCollection;
use App\Collections\SegmentCollection;
use App\Http\Repositories\MailChimpRepositoryInterface;
use App\Tests\TestCase;
use App\Http\Services\MailChimpFacade;
use App\Models\MailChimpList;
use App\DTOs\MailChimpListDto;
use DrewM\MailChimp\MailChimp;

class MailChimpFacadeTest extends TestCase
{
    private $mailChimp;
    private $mailChimpListDto;
    private $mailChimpList;
    private $mailChimpListObject;
    private $service;
    private $repository;

    public function setUp(): void
    {
        $this->mailChimpListObject = new \stdClass();
        $this->mailChimpListObject->name = 'mail chimp list';
        $this->mailChimpListObject->id = 'abc1234567';
        $this->mailChimpListObject->totalSubscribers = 0;
        $this->mailChimpListObject->clickRate = 0;
        $this->mailChimpListObject->openRate = 0;
        $this->mailChimpListObject->segments = null;
        $this->mailChimpListDto = new MailChimpListDto();
        $this->mailChimpListDto->name = 'mail chimp list';
        $this->mailChimpListDto->id = 'abc1234567';
        $this->mailChimpListDto->totalSubscribers = 0;
        $this->mailChimpListDto->clickRate = 0;
        $this->mailChimpListDto->openRate = 0;
        $this->mailChimpListDto->segments = null;
        $this->mailChimpList = new MailChimpList($this->mailChimpListDto);
        $this->mailChimp = $this->createMock('DrewM\MailChimp\MailChimp');
        $this->repository = $this->createMock(MailChimpRepositoryInterface::class);
        $this->service = new MailChimpFacade($this->mailChimp, $this->repository);
    }

    public function testCanGetListById()
    {
        $mailChimpListObject = new \stdClass();
        $mailChimpListObject->name = 'mail chimp list';
        $mailChimpListObject->id = 'abc1234567';
        $mailChimpListObject->totalSubscribers = 0;
        $mailChimpListObject->clickRate = 0;
        $mailChimpListObject->openRate = 0;
        $mailChimpListObject->segments = new SegmentCollection([]);
        $mailChimpListDto = new MailChimpListDto();
        $mailChimpListDto->name = 'mail chimp list';
        $mailChimpListDto->id = 'abc1234567';
        $mailChimpListDto->totalSubscribers = 0;
        $mailChimpListDto->clickRate = 0;
        $mailChimpListDto->openRate = 0;
        $mailChimpListDto->segments = new SegmentCollection([]);
        $mailChimpList = new MailChimpList($this->mailChimpListDto);

        $this->repository
            ->expects($this->once())
            ->method('getListById')
            ->with($this->mailChimp, 'abc1234567')
            ->willReturn($this->mailChimpListDto);

        $this->repository
            ->expects($this->once())
            ->method('getListSegments')
            ->with($this->mailChimp, 'abc1234567')
            ->willReturn(new SegmentCollection([]));

        $listModel = new MailChimpList($this->mailChimpListDto);
        $listModel->setSegments(new SegmentCollection([]));

        $actualResults = $this->service->getListById('abc1234567');

        $this->assertEquals($listModel, $actualResults);
    }

    public function testCannotGetListById_returnsNull()
    {
        $mailChimpListId = 'asdniasdiajsd';

        $this->repository
            ->expects($this->once())
            ->method('getListById')
            ->with($this->mailChimp, $mailChimpListId)
            ->willReturn(null);

        $actualResults = $this->service->getListById($mailChimpListId);

        $this->assertEmpty($actualResults);
    }

    public function testCanGetLists_returnsListCollection()
    {
        $collection = $this->createMock(ListCollection::class);

        $this->repository
            ->expects($this->once())
            ->method('getLists')
            ->with($this->mailChimp)
            ->willReturn($collection);

        $actualResults = $this->service->getLists();

        $this->assertInstanceOf(ListCollection::class, $actualResults);
    }
}
