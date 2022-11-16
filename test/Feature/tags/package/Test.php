<?php
namespace Test\Feature\tags\package;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParsePackage()
    {
        // when, then
        $this->assertParses(resource('tags/package.txt'), ['package' => 'PSR\Documentation\API']);
    }
}
