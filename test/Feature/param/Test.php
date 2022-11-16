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
        $this->assertParses(resource('param.many.txt'), [
            'params' => [
                [
                    'type'        => 'mixed[]',
                    'name'        => 'items',
                    'description' => "Array structure to count the elements of."
                ],
                [
                    'type'        => 'bool',
                    'name'        => 'recursive',
                    'description' => "Optional."
                ]
            ]]);
    }

    /**
     * @test
     */
    public function shouldParseParamMultiline()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('param.multiline.txt'), [
            'params' => [
                'recursive' => [
                    'type'        => 'bool',
                    'name'        => 'recursive',
                    'description' => "Optional. Whether or not to recursively\ncount elements in nested arrays.\nDefaults to `false`."
                ]
            ]]);
    }
}
