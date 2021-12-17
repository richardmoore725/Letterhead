<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;
use App\DTOs\ChannelDto;
use App\Models\Channel;
use Illuminate\Support\Collection;

/**
 * Class ChannelTest
 * @package App\Tests
 */
class ChannelTest extends TestCase
{
    /**
     * @var Channel
     */
    private $channel;

    public function setUp() : void
    {
        $channelConfigurations = new ChannelConfigurationCollection(new Collection());
        $dto = new ChannelDto();
        $dto->channelConfigurations = $channelConfigurations;

        $this->channel = new Channel($dto);
    }

    public function testCanConvertToArray_returnsArray()
    {
        $actualResults = $this->channel->convertToArray();
        $this->assertIsArray($actualResults);
    }
}
