<?php

namespace App\Models;

class Timezone
{
    private $label;
    private $offset;
    private $tzCode;

    public function __construct(\stdClass $object = null)
    {
        if (empty($object)) {
            return;
        }

        $this->label = isset($object->label) ? $object->label : '';
        $this->offset = isset($object->offset) ? $object->offset : '';
        $this->tzCode = isset($object->tzCode) ? $object->tzCode : '';
    }

    public function convertToArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'offset' => $this->getOffset(),
            'tzCode' => $this->getTzCode(),
        ];
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getOffset(): string
    {
        return $this->offset;
    }

    public function getTzCode(): string
    {
        return $this->tzCode;
    }
}
