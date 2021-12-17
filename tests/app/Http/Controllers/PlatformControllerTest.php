<?php

namespace App\Tests;

use App\Collections\UserPermissionCollection;
use App\Http\Controllers\PlatformController;
use Illuminate\Http\JsonResponse;

class PlatformControllerTest extends TestCase
{
    /**
     * @var PlatformController
     */
    private $controller;

    public function setup() : void
    {
        $this->controller = new PlatformController();
    }
    public function testCanGetPlatformUserAdministrates_returnsJsonResponse()
    {
        $collection = collect([1]);
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);
        $userPermissionCollection->expects($this->once())
            ->method('getPlatformIdsUserAdministrates')
            ->willReturn($collection);

        $actualResults = $this->controller->getPlatformsUserAdministrates($userPermissionCollection);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
}
