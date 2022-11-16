<?php
namespace Test\Feature\link;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseLink()
    {
        // when, then
        $this->assertParses(resource('tags/link.txt'), ['link' => 'https://example.com/my/bar']);
    }

    /**
     * @test
     */
    public function shouldParseLinkWithDescription()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('tags/link.description.txt'), [
            'link'        => 'https://example.com/my/bar',
            'description' => 'Documentation of Foo.'
        ]);
    }
}
