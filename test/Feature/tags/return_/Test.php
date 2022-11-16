<?php
namespace Test\Feature\tags\return_;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseReturn()
    {
        // when, then
        $this->assertParses(resource('tags/return.txt'), [
            'return' => [
                'type'        => 'string|null',
                'description' => "The label's text or null if none provided."
            ]
        ]);
    }
}
