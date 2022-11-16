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
    public function shouldParseApi()
    {
        // when, then
        $this->assertParses(resource('tags/api.txt'), ['api' => true]);
    }
}
