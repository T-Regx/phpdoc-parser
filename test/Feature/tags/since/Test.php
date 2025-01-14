<?php
namespace Test\Feature\tags\since;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseSince()
    {
        // when, then
        $this->assertParses(resource('tags/since.txt'), ['since' => '1.0.0']);
    }

    /**
     * @test
     */
    public function shouldParseSinceWithDescription()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('tags/since.description.txt'), [
            'since'       => '1.0.2',
            'description' => 'Added the $b argument.'
        ]);
    }
}
