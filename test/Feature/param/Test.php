<?php
namespace Test\Feature\param;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseParams()
    {
        // when, then
        $this->assertParses(resource('tags/param.many.txt'), [
            'params' => [
                [
                    'name'        => 'items',
                    'type'        => 'mixed[]',
                    'description' => "Array structure to count the elements of."
                ],
                [
                    'name'        => 'recursive',
                    'type'        => 'bool',
                    'description' => "Optional."
                ]
            ]]);
    }

    /**
     * @test
     */
    public function shouldParseParamMultiline()
    {
        // when, then
        $this->assertParses(resource('tags/param.multiline.txt'), [
            'params' => [
                [
                    'name'        => 'recursive',
                    'type'        => 'bool',
                    'description' => "Optional. Whether or not to recursively\ncount elements in nested arrays.\nDefaults to `false`."
                ]
            ]
        ]);
    }
}
