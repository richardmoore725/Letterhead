<?php

namespace App\Http\Repositories;

use App\DTOs\ConfigurationDto;

class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    public const TABLE_CONFIGURATIONS = 'configurations';

    public function getConfigurationBySlug(string $configurationSlug): ?ConfigurationDto
    {
        try {
            $result = app('db')
                ->table(self::TABLE_CONFIGURATIONS)
                ->where('configurationSlug', '=', $configurationSlug)
                ->first();

            return empty($result) ? null : new ConfigurationDto($result);
        } catch (\Exception $e) {
            return null;
        }
    }
}
