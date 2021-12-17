<?php

namespace App\Tests;

use App\Mail\MailTemplateGenerator;
use Illuminate\Mail\Mailable;

class MailTemplateGeneratorTest extends TestCase
{
    public function testCanBuildTemplate_returnsMailable()
    {
        $mailable = new MailTemplateGenerator('jack@whereby.us', 'Hey there!', 'Wahoo');
        $actualResults = $mailable->build();

        $this->assertInstanceOf(Mailable::class, $actualResults);
    }
}
