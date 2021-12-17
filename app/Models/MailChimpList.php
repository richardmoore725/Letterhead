<?php

namespace App\Models;

use App\DTOs\MailChimpListDto;
use App\Collections\SegmentCollection;

class MailChimpList
{
    private $clickRate;
    private $id;
    private $name;
    private $openRate;
    private $totalSubscribers;
    private $segments;

    public function __construct(MailChimpListDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->clickRate = $dto->clickRate;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->openRate = $dto->openRate;
        $this->totalSubscribers = $dto->totalSubscribers;
        $this->segments = $dto->segments;
    }

    public function convertToArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'totalSubscribers' => $this->totalSubscribers,
            'clickRate' => $this->clickRate,
            'openRate' => $this->openRate,
            'segments' => $this->segments,
        ];
    }

    public function getClickRate(): float
    {
        return $this->clickRate;
    }

    public function getOpenRate(): float
    {
        return $this->openRate;
    }

    public function getTotalSubscribers(): int
    {
        return $this->totalSubscribers;
    }

    public function getMailChimpListId(): string
    {
        return $this->id;
    }

    public function getSegments(): SegmentCollection
    {
        return $this->segments;
    }

    public function setSegments(SegmentCollection $segments): void
    {
        $this->segments = $segments;
    }
}
