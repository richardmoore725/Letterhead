<?php

namespace App\Models;

use App\DTOs\SegmentDto;

class Segment
{
    private $id;
    private $memberCount;
    private $name;

    public function __construct(SegmentDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->id = $dto->id;
        $this->memberCount = $dto->memberCount;
        $this->name = $dto->name;
    }

    public function convertToArray(): array
    {
        return [
            'id' => $this->id,
            'memberCount' => $this->memberCount,
            'name' => $this->name,
        ];
    }
}
