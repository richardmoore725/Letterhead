<?php

namespace app\Collections;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Tests\TestCase;

class MessageCollectionTest extends TestCase
{
    /**
     * @var MessageCollection
     */
    private $messageCollection;

    public function setUp() : void
    {
        $this->messageCollection = new MessageCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new MessageDto();
        $dto->createdAt = '2020-02-04';
        $dto->deletedAt = '2020-02-04';
        $dto->id = 1;
        $dto->message = 'test';
        $dto->resourceId = 1;
        $dto->resorceName = 'test';
        $dto->uniqueId = 'asd1as2d1';
        $dto->userId = 1;

        $arrayOfBrandDtos = [ $dto ];

        $this->messageCollection = new MessageCollection($arrayOfBrandDtos);

        $actualResults = $this->messageCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
        $messageObject = new \stdClass();
        $messageObject->created_at = '2020-02-04';
        $messageObject->deleted_at = '2020-02-04';
        $messageObject->id = 1;
        $messageObject->message = 'test';
        $messageObject->resourceId = 1;
        $messageObject->resourceName = 'test';
        $messageObject->uniqueId = 'asd1as2d1';
        $messageObject->userId = 1;

        $messageObjectsFromDatabase = collect([ $messageObject ]);

        $this->messageCollection = new MessageCollection($messageObjectsFromDatabase);

        $actualResults = $this->messageCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }
}
