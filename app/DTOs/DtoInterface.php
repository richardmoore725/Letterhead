<?php

namespace App\DTOs;

interface DtoInterface
{
    public function mapDtoToDatabaseColumnsArray(): array;
}
