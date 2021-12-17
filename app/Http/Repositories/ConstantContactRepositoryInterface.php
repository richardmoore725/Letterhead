<?php

namespace App\Http\Repositories;

use App\Http\Response;

interface ConstantContactRepositoryInterface
{
    public function getAccessToken($code): array;
    public function getNewAccessToken($refresh_token);
    public function getAccessTokenFromChannel($channel): string;
}
