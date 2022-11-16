<?php
namespace Test\Feature\ignore;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseIgnore()
    {
        // when, then
        $this->assertParses(resource('ignore.txt'), ['ignore' => true]);
    }
}
