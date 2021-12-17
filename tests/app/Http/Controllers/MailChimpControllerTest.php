<?php

namespace App\Tests;

use App\Collections\ListCollection;
use App\Http\Controllers\MailChimpController;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\MailChimpFacadeInterface;
use App\Models\MailChimpList;
use Illuminate\Http\JsonResponse;

class MailChimpControllerTest extends TestCase
{
    private $list;
    private $mailChimpFacade;
    private $controller;
    private $brandService;
    private $channelService;

    public function setup() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->controller = new MailChimpController($this->brandService, $this->channelService);
        $this->list = $this->createMock(MailChimpList::class);
        $this->mailChimpFacade = $this->createMock(MailChimpFacadeInterface::class);
    }

    public function testCanGetListById_returnsList()
    {
        $id = 2;
        $this->mailChimpFacade
            ->expects($this->once())
            ->method('getLIstById')
            ->with($id)
            ->willReturn($this->list);

        $this->list
            ->expects($this->once())
            ->method('convertToArray')
            ->willReturn([]);

        $actualResults = $this->controller->getListById($this->mailChimpFacade, $id);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('[]', $actualResults->getContent());
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotGetListById_returns404()
    {
        $id = 2;
        $this->mailChimpFacade
            ->expects($this->once())
            ->method('getLIstById')
            ->with($id)
            ->willReturn(null);

        $actualResults = $this->controller->getListById($this->mailChimpFacade, $id);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetLists_returnsArray()
    {
        $listCollection = $this->createMock(ListCollection::class);
        $this->mailChimpFacade->expects($this->once())
            ->method('getLists')
            ->willReturn($listCollection);

        $listCollection->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([]);

        $actualResults = $this->controller->getLists($this->mailChimpFacade);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('[]', $actualResults->getContent());
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}
