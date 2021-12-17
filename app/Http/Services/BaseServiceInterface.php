<?php

namespace App\Http\Services;

interface BaseServiceInterface
{
    public function generateUniqueIdentifier(): string;
}
