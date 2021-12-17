<?php

namespace App\Tests;

use App\Collections\ChannelSubscribersListCollection;
use App\DTOs\ChannelSubscribersListDto;
use App\Models\ChannelSubscribersList;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ChannelSubscribersListCollectionTest extends TestCase
{
    private $listCollection;

    public function setUp() : void
    {
        $this->listCollection = new ChannelSubscribersListCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new ChannelSubscribersListDto(new \stdClass);
        $dto->createdAt = '2021-01-11';
        $dto->deletedAt = '';
        $dto->description = 'Hello this is my first sparkpost list.';
        $dto->id = 1;
        $dto->name = 'black bitter coffee 1st list';
        $dto->updatedAt = '2021-01-11';
        $dto->uniqueId = 'unique_id_12345';


        $arrayOfListDtos = [ $dto ];

        $this->listCollection = new ChannelSubscribersListCollection($arrayOfListDtos);

        $actualResults = $this->listCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $listObject = new \stdClass();
       $listObject->createdAt = '2021-01-11';
       $listObject->deletedAt = '';
       $listObject->description = 'Hello this is my first sparkpost list.';
       $listObject->id = 1;
       $listObject->name = 'black bitter coffee 1st list';
       $listObject->updatedAt = '2021-01-11';
       $listObject->uniqueId = 'unique_id-12345';
       
       $listObjectsFromDatabase = collect([ $listObject ]);

       $this->listCollection = new ChannelSubscribersListCollection($listObjectsFromDatabase);

       $actualResults = $this->listCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
