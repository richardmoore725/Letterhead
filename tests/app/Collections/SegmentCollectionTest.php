<?php

namespace App\Tests;

use App\Collections\SegmentCollection;
use App\DTOs\SegmentDto;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class SegmentCollectionTest extends TestCase
{
    private $segmentCollection;

    public function setUp() : void
    {
        $this->segmentCollection = new SegmentCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new SegmentDto(new \stdClass);
        $dto->id = 1;
        $dto->memberCount = 1;
        $dto->name = 'cool ppl';

        $arrayOfSegmentDtos = [ $dto ];

        $this->segmentCollection = new SegmentCollection($arrayOfSegmentDtos);

        $actualResults = $this->segmentCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $segmentObject = new \stdClass();
       $segmentObject->id = 1;
       $segmentObject->memberCount = 1;
       $segmentObject->name = 'cool ppl';
       
       $segmentObjectsFromDatabase = collect([ $segmentObject ]);

       $this->segmentCollection = new SegmentCollection($segmentObjectsFromDatabase);

       $actualResults = $this->segmentCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
