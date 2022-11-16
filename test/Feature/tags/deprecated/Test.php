<?php
namespace Test\Feature\tags\deprecated;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseDeprecated()
    {
        // when, then
        $this->assertParses(resource('tags/deprecated.txt'), ['deprecated' => true]);
    }

    /**
     * @test
     */
    public function shouldParseDeprecatedVersionDescription()
    {
        // when, then
        $this->assertParses(resource('tags/deprecated.version.txt'), [
            'deprecated' => '1.0.0'
        ]);
    }

    /**
     * @test
     */
    public function shouldParseDeprecatedVersion()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('tags/deprecated.version.description.txt'), [
            'deprecated'  => '1.0.0',
            'description' => 'No longer used by internal code and not recommended.'
        ]);
    }
}
