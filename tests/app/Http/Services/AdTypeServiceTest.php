<?php

namespace App\Tests\Http;

use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Response;
use App\Http\Services\AdTypeService;
use App\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdTypeServiceTest extends TestCase
{
    private $file;
    private $request;
    private $repository;
    private $service;

    public function setUp(): void
    {
        $this->file = $this->createMock(UploadedFile::class);
        $this->request = $this->createMock(Request::class);
        $this->repository = $this->createMock(BeaconRepositoryInterface::class);
        $this->service = new AdTypeService($this->repository);
    }

    public function testCanGetAdTypeRequestFormattedForMultipartPost__noScreenshot_returnsArray()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('adImageHeight', 300);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('adImageWidth', 300);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('blurbCharacterCount', 140);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('brandId');

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('callToActionCharacterCount', 50);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('channelId');

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('contentCharacterCount', 50);

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('cpm', 0);

        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('daysPublished', []);


        $this->request
            ->expects($this->at(9))
            ->method('input')
            ->with('description', '');

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('descriptionCharacterCount', 140);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('headingCharacterCount', 50);

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('hasAdvertiserLogo', 'false');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('hasBlurb', 'true');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('hasCallToAction', 'true');

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('hasContent', 'false');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('hasCustomSchedule', 'false');

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('hasEmoji', 'false');

        $this->request
            ->expects($this->at(18))
            ->method('input')
            ->with('hasHeading', 'true');

        $this->request
            ->expects($this->at(19))
            ->method('input')
            ->with('hasImage', 'false');

        $this->request
            ->expects($this->at(20))
            ->method('input')
            ->with('inventory', 1);

        $this->request
            ->expects($this->at(21))
            ->method('input')
            ->with('order', 0);

        $this->request
            ->expects($this->at(22))
            ->method('input')
            ->with('positioning', 0);

        $this->request
            ->expects($this->at(23))
            ->method('input')
            ->with('title', '');

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->with('screenshot')
            ->willReturn(false);

        $actualResults = $this->service->getAdTypeRequestFormattedForMultipartPost($this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanGetAdTypeRequestFormattedForMultipartPost__WithScreenshot_returnsArray()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('adImageHeight', 300);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('adImageWidth', 300);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('blurbCharacterCount', 140);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('brandId');

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('callToActionCharacterCount', 50);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('channelId');

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('contentCharacterCount', 50);

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('cpm', 0);

        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('daysPublished', []);


        $this->request
            ->expects($this->at(9))
            ->method('input')
            ->with('description', '');

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('descriptionCharacterCount', 140);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('headingCharacterCount', 50);

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('hasAdvertiserLogo', 'false');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('hasBlurb', 'true');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('hasCallToAction', 'true');

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('hasContent', 'false');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('hasCustomSchedule', 'false');

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('hasEmoji', 'false');

        $this->request
            ->expects($this->at(18))
            ->method('input')
            ->with('hasHeading', 'true');

        $this->request
            ->expects($this->at(19))
            ->method('input')
            ->with('hasImage', 'false');

        $this->request
            ->expects($this->at(20))
            ->method('input')
            ->with('inventory', 1);

        $this->request
            ->expects($this->at(21))
            ->method('input')
            ->with('order', 0);

        $this->request
            ->expects($this->at(22))
            ->method('input')
            ->with('positioning', 0);

        $this->request
            ->expects($this->at(23))
            ->method('input')
            ->with('title', '');

        $this->request
            ->expects($this->at(24))
            ->method('hasFile')
            ->with('screenshot')
            ->willReturn(true);

        $this->request
            ->expects($this->at(25))
            ->method('file')
            ->with('screenshot')
            ->willReturn($this->file);

        $this->file
            ->expects($this->at(0))
            ->method('path')
            ->willReturn('https://google.com');

        $this->file
            ->expects($this->at(1))
            ->method('getClientOriginalName')
            ->willReturn('OriginalName');

        $actualResults = $this->service->getAdTypeRequestFormattedForMultipartPost($this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanScaffoldDefaultPromotionTypesForNewChannel_returnsFalse()
    {
        $expectedEndpoint = 'https://adservice.local/brands/5/channels/2/types/scaffold';
        $expectedKey = 'wee';

        $this->repository
            ->expects($this->once())
            ->method('createBrandChannelResourceFromService')
            ->with($expectedEndpoint, $expectedKey, 5, 2, null, false)
            ->willReturn(null);

        $actualResults = $this->service->scaffoldDefaultPromotionTypesForNewChannel(5, 2);

        $this->assertFalse($actualResults);
    }

    public function testCanScaffoldDefaultPromotionTypesForNewChannel_returnsTrue()
    {
        $expectedEndpoint = 'https://adservice.local/brands/5/channels/2/types/scaffold';
        $expectedKey = 'wee';

        $this->repository
            ->expects($this->once())
            ->method('createBrandChannelResourceFromService')
            ->with($expectedEndpoint, $expectedKey, 5, 2, null, false)
            ->willReturn(true);

        $actualResults = $this->service->scaffoldDefaultPromotionTypesForNewChannel(5, 2);

        $this->assertTrue($actualResults);
    }

    public function testCannotGetAvailableDatesByAdType_returnsResponse()
    {
        $error = new \stdClass();
        $error->message = 'Hey';
        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('getResponseFromApi')
            ->willReturn($response);

        $actualResults = $this->service->getAvailableDatesByAdType(6, 7, 8, ["2020-12-21"]);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGetAdTypesWithPricesByChannel_returnsResponse()
    {
        $error = new \stdClass();
        $error->message = 'Catchphrase!';
        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('getResponseFromApi')
            ->willReturn($response);
            $actualResults = $this->service->getAdTypesWithPricesByChannel(1, 1, 24000);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCannotUpdatePromotionTypeTemplate_repoError()
    {
        $error = new \stdClass();
        $error->message = 'Hey';
        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('getResponseFromApi')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($error);

        $response->expects($this->once())
            ->method('getStatus')
            ->willReturn(400);

        $actualResults = $this->service->updatePromotionTypeTemplate(3, 4, 5, 'wee');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatus());
        $this->assertEquals('Hey', $actualResults->getEndUserMessage());
    }

    public function testCanUpdatePromotionTypeTemplate_repoSuccess()
    {
        $error = new \stdClass();
        $error->message = 'Hey';
        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('getResponseFromApi')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $actualResults = $this->service->updatePromotionTypeTemplate(3, 4, 5, 'wee');

        $this->assertInstanceOf(Response::class, $actualResults);
    }
}
