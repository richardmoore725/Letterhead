<?php

namespace App\Tests\Formatters;

use App\Collections\UserCollection;
use App\Formatters\LetterDeltaMJMLFormatter;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\Promotion;
use App\Tests\TestCase;

class LetterDeltaMJMLFormatterTest extends TestCase
{
    private $channel;
    private $delta;
    private $formatter;
    private $letter;
    private $promotionsArray;
    private $userCollection;

    public function setUp() : void
    {
        $this->channel = $this->createMock(Channel::class);
        $this->delta = '{"ops":[{"insert":"We are going to write some content, add some headings, and insert some images.\n\nFirst section heading"},{"attributes":{"header":2},"insert":"\n"},{"insert":"Write your heart ♥ out.\n"},{"insert":{"image":"https://wherebyspace.nyc3.digitaloceanspaces.com/platformservice/brands/163/channels/3/channelId-3-1612575830-image.jpeg"}},{"insert":"\n\nLet\'s add another section.\n"},{"insert":{"segmentSection":{"mergeTag":"*|IFNOT:Membership = Individual Subscriber|*","segmentName":"nonmembers","src":"https://wherebyspace.nyc3.digitaloceanspaces.com/letterhead/segment-section-nonmember-begin.svg"}}},{"insert":"This is a non member section\nLet\'s put a heading in this section and excludei t.\n"},{"insert":{"segmentSection":{"mergeTag":"*|END:IF|*","segmentName":"nonmembers","src":"https://wherebyspace.nyc3.digitaloceanspaces.com/letterhead/segment-section-nonmember-end.svg"}}},{"insert":"\nA second heading"},{"attributes":{"header":2},"insert":"\n"},{"insert":"Write your heart ♥ out.\n"}]}';
        $this->formatter = new LetterDeltaMJMLFormatter();
        $this->letter = $this->createMock(Letter::class);
        $this->userCollection = $this->createMock(UserCollection::class);
    }

    public function testCanRenderMjmlTemplate()
    {
        $authorNames = [
            'Jack Black',
        ];

        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $promotion->expects($this->once())
            ->method('getMjml')
            ->willReturn('<mj-section></mj-section>');

        $promotion->expects($this->once())
            ->method('getPositioning')
            ->willReturn(33);

        $this->letter
            ->expects($this->once())
            ->method('setMjmlTemplate');

        $this->userCollection
            ->expects($this->once())
            ->method('getArrayOfUserFullNames')
            ->willReturn($authorNames);

        $this->letter
            ->expects($this->exactly(2))
            ->method('getSpecialBanner')
            ->willReturn('https://placehold.it/500x500');

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $this->delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanRenderMjmlTemplate_multipleAuthors()
    {
        $authorNames = [
            'Jack Black',
            'Jun Blue',
            'Charles Green',
        ];

        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $promotion->expects($this->once())
            ->method('getMjml')
            ->willReturn('<mj-section></mj-section>');

        $promotion->expects($this->once())
            ->method('getPositioning')
            ->willReturn(33);

        $this->letter
            ->expects($this->once())
            ->method('setMjmlTemplate');

        $this->userCollection
            ->expects($this->once())
            ->method('getArrayOfUserFullNames')
            ->willReturn($authorNames);

        $this->letter
            ->expects($this->exactly(2))
            ->method('getSpecialBanner')
            ->willReturn('https://placehold.it/500x500');

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $this->delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanRenderMjmlTemplate_twoAuthors()
    {
        $authorNames = [
            'Jack Black',
            'Jun Blue',
        ];

        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $promotion->expects($this->once())
            ->method('getMjml')
            ->willReturn('<mj-section></mj-section>');

        $promotion->expects($this->once())
            ->method('getPositioning')
            ->willReturn(33);

        $this->letter
            ->expects($this->once())
            ->method('setMjmlTemplate');

        $this->userCollection
            ->expects($this->once())
            ->method('getArrayOfUserFullNames')
            ->willReturn($authorNames);

        $this->letter
            ->expects($this->exactly(2))
            ->method('getSpecialBanner')
            ->willReturn('https://placehold.it/500x500');

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $this->delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanRenderMjmlTemplate_emptyAuthors()
    {
        $authorNames = [];

        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $promotion->expects($this->once())
            ->method('getMjml')
            ->willReturn('<mj-section></mj-section>');

        $promotion->expects($this->once())
            ->method('getPositioning')
            ->willReturn(33);

        $this->letter
            ->expects($this->once())
            ->method('setMjmlTemplate');

        $this->userCollection
            ->expects($this->once())
            ->method('getArrayOfUserFullNames')
            ->willReturn($authorNames);

        $this->letter
            ->expects($this->exactly(2))
            ->method('getSpecialBanner')
            ->willReturn('https://placehold.it/500x500');

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $this->delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanRenderMjmlTemplate_usesChannelLogo()
    {
        $authorNames = [
            'Jack Black',
        ];

        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $promotion->expects($this->once())
            ->method('getMjml')
            ->willReturn('<mj-section></mj-section>');

        $promotion->expects($this->once())
            ->method('getPositioning')
            ->willReturn(33);

        $this->letter
            ->expects($this->once())
            ->method('setMjmlTemplate');

        $this->userCollection
            ->expects($this->once())
            ->method('getArrayOfUserFullNames')
            ->willReturn($authorNames);

        $this->letter
            ->expects($this->exactly(1))
            ->method('getSpecialBanner')
            ->willReturn('');

        $this->channel->expects($this->once())
            ->method('getCHannelHorizontalLogo')
            ->willReturn('https://placehold.it/500x500');

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $this->delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCannotRenderMjmlTemplate()
    {
        $delta = '';
        $promotion = $this->createMock(Promotion::class);
        $promotions = [
            $promotion,
        ];

        $actualResults = $this->formatter->renderMjmlTemplate(
            $this->userCollection,
            $this->channel,
            $delta,
            $this->letter,
            $promotions
        );

        $this->assertEquals(500, $actualResults->getStatus());
        $this->assertInstanceOf(Response::class, $actualResults);
    }
}

