<?php

namespace App\DTOs;

class MailChimpListDto
{
    public $name;
    public $id;
    public $totalSubscribers;
    public $clickRate;
    public $openRate;
    public $segments;

    public function __construct(\stdClass $object = null)
    {
        if (empty($object)) {
            return;
        }

        $this->name = isset($object->name) ? $object->name : '';
        $this->id = isset($object->id) ? $object->id : '';
        $this->totalSubscribers = isset($object->stats->member_count) ? (int) $object->stats->member_count : 0;
        $this->clickRate = isset($object->stats->click_rate) ? round($object->stats->click_rate * 0.01, 2) : 0;
        $this->openRate = isset($object->stats->open_rate) ? round($object->stats->open_rate * 0.01, 2) : 0;
        $this->segments = isset($object->segments) ? $object->segments : null;
    }
}
