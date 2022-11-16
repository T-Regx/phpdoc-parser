<?php
namespace Test\Feature\internal;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseInternal()
    {
        // when, then
        $this->assertParses(resource('tags/internal.txt'), ['internal' => true]);
    }
}
