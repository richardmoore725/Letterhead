<?php

namespace App\Http\Repositories;

use App\DTOs\ConfigurationDto;

interface ConfigurationRepositoryInterface
{
    public function getConfigurationBySlug(string $configurationSlug): ?ConfigurationDto;
}
