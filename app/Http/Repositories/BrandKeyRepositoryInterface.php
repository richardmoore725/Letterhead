<?php

namespace App\Http\Repositories;

interface BrandKeyRepositoryInterface
{
    public function getBrandKeyByBrandId(int $brandId): ?string;
}
