<?php
namespace App\Tests;

use App\Http\Middleware\ValidateChannelDataMiddleware;
use App\Http\Services\BrandServiceInterface;
use App\Models\Channel;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;


class ValidateChannelDataMiddlewareTest extends TestCase
{
    private $request;
    private $middleware;

    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->file = $this->createMock(UploadedFile::class);
        $this->middleware = new ValidateChannelDataMiddleware($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testValidatorFails_returns400JsonResponse()
    {
        $testChannelName = "testChannelName";
        $testChannelSlug = "testChannelSlug";

        $messageBag = $this->createMock(MessageBag::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "channelName" => $testChannelName,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);

        $validator
            ->shouldReceive('errors')
            ->once()
            ->andReturn($messageBag);

        $messageBag
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testChannelIsValidWithoutUploadedChannelImage_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(false);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('channelImage')
            ->willReturn(null);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(10))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle); 

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithNullChannelImageString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelImage = "null";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(false);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('channelImage')
            ->willReturn($testChannelImage);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(10))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithChannelImageString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelImage = "testChannelImage";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(false);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('channelImage')
            ->willReturn($testChannelImage);

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('channelImage')
            ->willReturn($testChannelImage);

        $this->request
            ->expects($this->at(8))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(9))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(10))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(11))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(18))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithoutUploadedChannelHorizontalLogo_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('channelHorizontalLogo')
            ->willReturn(null);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(10))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);
        
        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithNullChannelHorizontalLogoString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelHorizontalImage = "null";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);
           
        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('channelHorizontalLogo')
            ->willReturn($testChannelHorizontalImage);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(10))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');
           
        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithChannelHorizontalLogoString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelHorizontalImage = "testChannelHorizontalImage";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('channelHorizontalLogo')
            ->willReturn($testChannelHorizontalImage);

        $this->request
            ->expects($this->at(9))
            ->method('input')
            ->with('channelHorizontalLogo')
            ->willReturn($testChannelHorizontalImage);

        $this->request
            ->expects($this->at(10))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(11))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);
            
        $this->request
            ->expects($this->at(18))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithoutUploadedChannelSquareLogo_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('channelSquareLogo')
            ->willReturn(null);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);
            
        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithNullChannelSquareLogoString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSquareImage = "null";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);
        
        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('channelSquareLogo')
            ->willReturn($testChannelSquareImage);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidWithChannelSquareLogoString_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSquareImage = "testChannelSquareImage";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(10))
            ->method('input')
            ->with('channelSquareLogo')
            ->willReturn($testChannelSquareImage);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('channelSquareLogo')
            ->willReturn($testChannelSquareImage);

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(18))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelIsValidated_returnsClosure()
    {
        $testChannelDescription = "testChannelDescription";
        $testChannelSlug = "testChannelSlug";
        $testTitle = "testTitle";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateChannel" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brand" => "testBrand",
                "channelDescription" => $testChannelDescription,
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->at(0))
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('accentColor')
            ->willReturn('#badass');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('channelSlug')
            ->willReturn($testChannelSlug);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('channelDescription')
            ->willReturn($testChannelDescription);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('channelImage')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('channelImage')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(7))
            ->method('hasFile')
            ->with('channelHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(8))
            ->method('file')
            ->with('channelHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(9))
            ->method('hasFile')
            ->with('channelSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(10))
            ->method('file')
            ->with('channelSquareLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(11))
            ->method('input')
            ->with('defaultEmailFromName')
            ->willReturn('jun');

        $this->request
            ->expects($this->at(12))
            ->method('input')
            ->with('defaultFromEmailAddress')
            ->willReturn('junsu@whereby.us');

        $this->request
            ->expects($this->at(13))
            ->method('input')
            ->with('defaultFont')
            ->willReturn('serif');

        $this->request
            ->expects($this->at(14))
            ->method('input')
            ->with('enableChannelAuthoring')
            ->willReturn(false);

        $this->request
            ->expects($this->at(15))
            ->method('input')
            ->with('headingFont')
            ->willReturn('');

        $this->request
            ->expects($this->at(16))
            ->method('input')
            ->with('loadPromosBeforeHeadings')
            ->willReturn(false);

        $this->request
            ->expects($this->at(17))
            ->method('input')
            ->with('title')
            ->willReturn($testTitle);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}
