<?php
namespace Test\Feature\version;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseVersion()
    {
        // when, then
        $this->assertParses(resource('version.txt'), ['version' => '1.5.0']);
    }

    /**
     * @test
     */
    public function shouldParseVersionWithDescription()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('version.description.txt'), [
            'version'     => '1.0.2',
            'description' => 'Added the $b argument.'
        ]);
    }
}