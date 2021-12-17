<?php

namespace App\Http\Controllers;

use App\Collections\UserPermissionCollection;
use Illuminate\Http\JsonResponse;

/**
 * PlatformController specifically orchestrates actions dedicated to high-level platform
 * management, as oppose to specific brand management, or channels.
 *
 * Class PlatformController
 * @package App\Http\Controllers
 */
class PlatformController extends Controller
{
    public function getPlatformsUserAdministrates(UserPermissionCollection $permissions): JsonResponse
    {
        $platformIds = $permissions->getPlatformIdsUserAdministrates()->all();
        return response()->json($platformIds);
    }
}
