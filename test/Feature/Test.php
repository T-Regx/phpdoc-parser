<?php
namespace Test\Feature;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
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
    public function shouldParseSimplePhpDoc()
    {
        // when, then
        $this->assertParses(resource('standard/simple.txt'), [
            'params' => [
                0 => [
                    'name' => 'nameOrIndex',
                    'type' => 'string|int'
                ]
            ],
            'return' => [
                'type' => 'string'
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseStandardExample()
    {
        // when, then
        $this->assertParses(resource('standard/example.txt'), [
            'summary'     => 'A summary informing the user what the associated element does.',
            'description' => 'A *description*, that can span multiple lines, to go _in-depth_ into
the details of this element and to provide some background information
or textual references.',
            'params'      => [
                [
                    'name'        => 'myArgument',
                    'type'        => 'string',
                    'description' => "With a *description* of this argument,\nthese may also span multiple lines."
                ]
            ],
            'return'      => [
                'type' => 'void'
            ],
        ]);
    }

    /**
     * @test
     */
    public function shouldParseCrlf()
    {
        // given
        $crlfDoc = "/**\r\n * @deprecated\r\n */";
        // when, then
        $this->assertParses($crlfDoc, ['deprecated' => true,]);
    }

    /**
     * @test
     */
    public function shouldParseCr()
    {
        // given
        $crlfDoc = "/**\r * @deprecated\r */";
        // when, then
        $this->assertParses($crlfDoc, ['deprecated' => true,]);
    }

    /**
     * @test
     */
    public function shouldParseCrNewlines()
    {
        // given
        $crlfDoc = "/** summary\r*\r*description */";
        // when, then
        $this->assertParses($crlfDoc, [
            'summary'     => 'summary',
            'description' => 'description',
        ]);
    }

    /**
     * @test
     * @dataProvider improperDocBlocks
     */
    public function shouldFailForString(string $docString)
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage('Failed to parse');
        // when
        $parser->parse($docString);
    }

    public function improperDocBlocks(): array
    {
        return [
            ['test'],
            ['/* test'],
            ['/* test *'],
            ['test */'],
            ['test **/'],

            ['/** test'],
            ['/** test *'],

            [' test */'],
            ['* test */'],
            ['/* test */'],

            ['/** test */ test */ '],
        ];
    }
}
