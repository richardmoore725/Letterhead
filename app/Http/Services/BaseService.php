<?php

namespace App\Http\Services;

use RandomLib\Factory;

class BaseService implements BaseServiceInterface
{
    /**
     * @return string
     */
    public function generateUniqueIdentifier(): string
    {
        $charactersToComposeKey = 'abcdefghiklmnopqrstuvwxyz0123456789';
        $randomStringFactory = new Factory();
        $randomStringGenerator = $randomStringFactory->getMediumStrengthGenerator();

        return $randomStringGenerator->generateString(10, $charactersToComposeKey);
    }
}
