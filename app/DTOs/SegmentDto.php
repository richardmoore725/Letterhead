<?php

namespace App\DTOs;

class SegmentDto
{
    public $id;
    public $memberCount;
    public $name;

    public function __construct(\stdClass $object)
    {
        $this->id = isset($object->id) ? $object->id : null;
        $this->memberCount = isset($object->memberCount) ? $object->memberCount : 0;
        $this->name = isset($object->name) ? $object->name : '';
    }
}
