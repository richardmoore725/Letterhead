<?php

namespace App\Tests;

use App\Http\Response;

class ResponseTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testCanGetBooleanFromResponseBody_returnsTrue()
    {
        $response = new Response('', 200, true);

        $actualResults = $response->getBooleanFromResponseBody();

        $this->assertTrue($actualResults);
    }

    public function testCanGetBooleanFromResponseBody_notBoolean_returnsFalse()
    {
        $response = new Response('', 200, 'weee');

        $actualResults = $response->getBooleanFromResponseBody();

        $this->assertFalse($actualResults);
    }
}
