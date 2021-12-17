<?php

namespace App\Tests;

use App\Collections\BrandCollection;
use App\Collections\BrandConfigurationCollection;
use App\DTOs\BrandDto;
use App\Models\Brand;
use Illuminate\Support\Collection;

class BrandCollectionTest extends TestCase
{
    /**
     * @var BrandCollection
     */
    private $brandCollection;

    public function setUp() : void
    {
        $this->brandCollection = new BrandCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $brandConfigurations = new BrandConfigurationCollection(new Collection());

        $dto = new BrandDto();
        $dto->brandConfigurations = $brandConfigurations;
        $dto->brandHorizontalLogo = '';
        $dto->brandName = 'Name';
        $dto->brandSlug = 'Slug';
        $dto->brandSquareLogo = '';
        $dto->createdAt = '2020-02-04';
        $dto->id = 1;
        $dto->updatedAt = '2020-03-01';

        $arrayOfBrandDtos = [ $dto ];

        $this->brandCollection = new BrandCollection($arrayOfBrandDtos);

        $actualResults = $this->brandCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $brandConfigurations = new BrandConfigurationCollection(new Collection());

       $brandObject = new \stdClass();
       $brandObject->brandConfigurations = $brandConfigurations;
       $brandObject->brandHorizontalLogo = '';
       $brandObject->brandName = 'Name';
       $brandObject->brandSlug = 'Slug';
       $brandObject->brandSquareLogo = '';
       $brandObject->createdAt = '2020-02-04';
       $brandObject->id = 1;
       $brandObject->updatedAt = '2020-03-01';
       
       $brandObjectsFromDatabase = collect([ $brandObject ]);

       $this->brandCollection = new BrandCollection($brandObjectsFromDatabase);

       $actualResults = $this->brandCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
