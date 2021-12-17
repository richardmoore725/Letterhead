<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

interface PackageServiceInterface
{
    public function getPackageResourcesFromAdService(int $brandId, int $channelId, string $path);
    public function getPackageRequestFormattedForMultipartPost(Request $request): array;
}
