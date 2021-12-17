<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class UserPermissionCollection extends BaseCollection
{
    /**
     * UserPermissionCollection constructor.
     * @param Collection|null $permissions
     */
    public function __construct($permissions)
    {
        parent::__construct($permissions);
    }

    public function getBrandIdsUserAdministrates(): Collection
    {
        return $this->getCreatorPermissionsForSpecificResource('brand')
            ->map(function ($brandPermission) {
                return $brandPermission->resourceId;
            });
    }

    private function getCreatorPermissionsForSpecificResource(string $resource): UserPermissionCollection
    {
        return $this->filter(function ($permission) use ($resource) {
            $isCreateAction = $permission->action === 'create';
            $isSpecificResource = $permission->resource === $resource;

            return $isSpecificResource && $isCreateAction;
        });
    }

    public function getPlatformIdsUserAdministrates(): Collection
    {
        return $this->getCreatorPermissionsForSpecificResource('platform')
            ->map(function ($platformPermission) {
                return $platformPermission->resourceId;
            });
    }

    public function canUserAdministrateChannel(int $brandId, int $channelId, UserPermissionCollection $permissions): bool
    {
        $creatorLevelBrandPermissions = $this->getCreatorPermissionsForSpecificResource('brand');
        $brandPermissions = $creatorLevelBrandPermissions->first(function ($value, $key) use ($brandId) {
            return $value->resourceId === $brandId;
        });

        if (empty($brandPermissions)) {
            $creatorLevelChannelPermissions = $this->getCreatorPermissionsForSpecificResource('channel');
            $channelPermissions = $creatorLevelChannelPermissions->first(function ($value, $key) use ($channelId) {
                return $value->resourceId === $channelId;
            });

            if (empty($channelPermissions)) {
                return false;
            }
        }

        return true;
    }
}
