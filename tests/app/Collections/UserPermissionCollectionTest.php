<?php

namespace App\Tests;

use App\Collections\UserPermissionCollection;
use Illuminate\Support\Collection;

class UserPermissionCollectionTest extends TestCase
{
    private $createBrandPermission;
    private $createPlatformPermission;
    private $permissions;
    private $collection;

    public function setUp() : void
    {
        $this->createBrandPermission = new \stdClass();
        $this->createBrandPermission->action = 'create';
        $this->createBrandPermission->resource = 'brand';
        $this->createBrandPermission->resourceId = 4;
        $this->createChannelPermission = new \stdClass();
        $this->createChannelPermission->action = 'create';
        $this->createChannelPermission->resource = 'channel';
        $this->createChannelPermission->resourceId = 2;
        $this->createPlatformPermission = new \stdClass();
        $this->createPlatformPermission->action = 'create';
        $this->createPlatformPermission->resource = 'platform';
        $this->createPlatformPermission->resourceId = 1;
        $this->permissions = [
            $this->createBrandPermission,
            $this->createChannelPermission, 
            $this->createPlatformPermission,
        ];

        $this->collection = new UserPermissionCollection($this->permissions);
    }

    public function testCanGetBrandIsUserAdministrates_returnsCollection()
    {
        $expectedResults = collect([$this->createBrandPermission->resourceId]);
        $actualResults = $this->collection->getBrandIdsUserAdministrates();

        $this->assertInstanceOf(Collection::class, $actualResults);
        $this->assertEquals($expectedResults, $actualResults);
    }

    public function testCanGetPlatformIdsUserAdministrates_returnsCollection()
    {
        $actualResults = $this->collection->getPlatformIdsUserAdministrates();

        $this->assertInstanceOf(Collection::class, $actualResults);
    }

    public function testCanUserAdministrateChannel_returnsTrue_withBrandPermissions()
    {
        $creatorLevelBrandPermissions = collect([$this->createBrandPermission->resourceId]);

        $actualResults = $this->collection->canUserAdministrateChannel(4, 1, $this->collection);

        $this->assertTrue($actualResults);
    }

    public function testCanUserAdministrateChannel_returnsTrue_withChannelPermissions()
    {
        $creatorLevelBrandPermissions = collect([$this->createBrandPermission->resourceId]);
        $creatorLevelChannelPermissions = collect([$this->createChannelPermission->resourceId]);

        $actualResults = $this->collection->canUserAdministrateChannel(3, 2, $this->collection);

        $this->assertTrue($actualResults);
    }

    public function testCanUserAdministrateChannel_returnsFalse()
    {
        $creatorLevelBrandPermissions = collect([$this->createBrandPermission->resourceId]);
        $creatorLevelChannelPermissions = collect([$this->createChannelPermission->resourceId]);

        $actualResults = $this->collection->canUserAdministrateChannel(3, 1, $this->collection);

        $this->assertFalse($actualResults);
    }
}
