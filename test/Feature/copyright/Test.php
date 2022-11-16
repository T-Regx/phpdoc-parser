<?php
namespace Test\Feature\copyright;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseCopyright()
    {
        // when, then
        $this->assertParses(resource('copyright.txt'), [
            'copyright' => '1997-2005 The PHP Group'
        ]);
    }
}
