<?php

namespace App\Tests;

use App\Collections\ChannelCollection;
use App\Collections\ChannelConfigurationCollection;
use App\DTOs\ChannelDto;
use App\Models\Channel;
use Illuminate\Support\Collection;

class ChannelCollectionTest extends TestCase
{
    /**
     * @var ChannelCollection
     */
    private $channelCollection;

    public function setUp() : void
    {
        $this->channelCollection = new ChannelCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $channelConfigurations = new ChannelConfigurationCollection(new Collection());

        $dto = new ChannelDto();
        $dto->brandId = 1;
        $dto->channelConfigurations = $channelConfigurations;
        $dto->channelHorizontalLogo = '';
        $dto->channelSlug = 'Slug';
        $dto->channelDescription = 'Description';
        $dto->channelImage = '';
        $dto->channelSquareLogo = '';
        $dto->title = 'Title';
        $dto->createdAt = '2020-02-04';
        $dto->id = 1;
        $dto->updatedAt = '2020-03-01';

        $arrayOfChannelDtos = [ $dto ];

        $this->channelCollection = new ChannelCollection($arrayOfChannelDtos);

        $actualResults = $this->channelCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
        $channelConfigurations = new ChannelConfigurationCollection(new Collection());

       $channelObject = new \stdClass();
       $channelObject->brandId = 1;
       $channelObject->channelConfigurations = $channelConfigurations;
       $channelObject->channelHorizontalLogo = '';
       $channelObject->channelSlug = 'Slug';
       $channelObject->channelDescription = 'Description';
       $channelObject->channelImage = '';
       $channelObject->channelSquareLogo = '';
       $channelObject->title = 'Title';
       $channelObject->createdAt = '2020-02-04';
       $channelObject->id = 1;
       $channelObject->updatedAt = '2020-03-01';
       
       $channelObjectsFromDatabase = collect([ $channelObject ]);

       $this->channelCollection = new ChannelCollection($channelObjectsFromDatabase);

       $actualResults = $this->channelCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
