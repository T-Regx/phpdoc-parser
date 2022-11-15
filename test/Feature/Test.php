<?php
namespace Test\Feature;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseEmpty()
    {
        // given
        $this->assertParses(resource('empty.txt'), []);
    }
}
