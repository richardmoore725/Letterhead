<?php

namespace App\Tests\Http;

use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Services\PackageService;
use App\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class PackageServiceTest extends TestCase
{
    private $beaconRepository;
    private $file;
    private $request;
    private $service;

    public function setUp(): void
    {
        $this->beaconRepository = $this->createMock(BeaconRepositoryInterface::class);
        $this->file = $this->createMock(UploadedFile::class);
        $this->request = $this->createMock(Request::class);
        $this->service = new PackageService($this->beaconRepository);
    }

    public function testCanGetPackageRequestFormattedForMultipartPost__doesntHavePackageImage_returnsArray()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('adTypesInPackage', [])
            ->willReturn([]);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('brandId')
            ->willReturn(1);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('channelId')
            ->willReturn(1);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('description')
            ->willReturn('Wee');

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('displayOrder')
            ->willReturn(1);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('isDisplayed', true)
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('name')
            ->willReturn('Name');

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('price')
            ->willReturn(9);

        $actualResults = $this->service->getPackageRequestFormattedForMultipartPost($this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanGetPackageRequestFormattedForMultipartPost__hasPackageImage_returnsArray()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('adTypesInPackage', [])
            ->willReturn([]);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('brandId');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('channelId');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('description');

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('displayOrder');

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('isDisplayed', true);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('name');

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('price');

            $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('hasDiscount')
            ->willReturn(false);

        $this->request
            ->expects($this->at(9))
            ->method('input')
            ->with('hasPercentageDiscount')
            ->willReturn(false);

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('discount')
            ->willReturn(0.0);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('useFlatFee')
            ->willReturn(true);

        $this->request->expects($this->at(12))
            ->method('hasFile')
            ->with('packageImage')
            ->willReturn(true);

        $this->request->expects($this->at(13))
            ->method('file')
            ->with('packageImage')
            ->willReturn($this->file);

        $this->file->expects($this->at(0))
            ->method('path')
            ->willReturn('https://google.com');

        $this->file->expects($this->at(1))
            ->method('getClientOriginalName')
            ->willReturn('OriginalName');

        $actualResults = $this->service->getPackageRequestFormattedForMultipartPost($this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanGetPackageResourcesFromAdService_returnsArray()
    {
        $this->beaconRepository
            ->expects($this->once())
            ->method('getAdResourceFromService')
            ->willReturn([]);

        $actualResults = $this->service->getPackageResourcesFromAdService(1, 1, '?displayHidden=true');

        $this->assertIsArray($actualResults);
    }
}
