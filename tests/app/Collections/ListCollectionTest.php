<?php

namespace App\Tests;

use App\Collections\ListCollection;
use App\DTOs\MailChimpListDto;
use App\Models\MailChimpList;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ListCollectionTest extends TestCase
{
    private $listCollection;

    public function setUp() : void
    {
        $this->listCollection = new ListCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new MailChimpListDto();
        $dto->clickRate = 0.4;
        $dto->id = 1;
        $dto->name = 'mailChimpList';
        $dto->openRate = 0.6;
        $dto->totalSubscribers = 100;

        $arrayOfMailChimpListDtos = [ $dto ];

        $this->listCollection = new ListCollection($arrayOfMailChimpListDtos);

        $actualResults = $this->listCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $listObject = new \stdClass();
       $listObject->clickRate = 0.4;
       $listObject->id = 1;
       $listObject->name = 'mailChimpList';
       $listObject->openRate = 0.6;
       $listObject->totalSubscribers = 100;
       
       $listObjectsFromDatabase = collect([ $listObject ]);

       $this->listCollection = new ListCollection($listObjectsFromDatabase);

       $actualResults = $this->listCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
