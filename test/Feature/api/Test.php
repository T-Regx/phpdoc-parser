<?php
namespace Test\Feature\api;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseAuthor()
    {
        // when, then
        $this->assertParses(resource('api.txt'), ['api' => true]);
    }
}
