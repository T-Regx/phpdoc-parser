<?php
namespace Test\Feature;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseEmpty()
    {
        // when, then
        $this->assertParses(resource('empty.txt'), []);
    }

    /**
     * @test
     */
    public function shouldParseStandardExample()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('standard/example.txt'), [
            'params'      => [
                [
                    'type'        => 'string',
                    'name'        => 'myArgument',
                    'description' => 'With a *description* of this argument,  these may also span multiple lines.'
                ]
            ],
            'return'      => [
                'type' => 'void'
            ],
            'summary'     => 'A summary informing the user what the associated element does.',
            'description' => 'A *description*, that can span multiple lines, to go _in-depth_ into
 the details of this element and to provide some background information
 or textual references.',
        ]);
    }
}
