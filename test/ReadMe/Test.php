<?php
namespace Test\ReadMe;

use Jasny\PHPDocParser\PHPDocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use Jasny\PhpdocParser\Tag\FlagTag;
use Jasny\PhpdocParser\Tag\SummaryAndDescription;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    /**
     * @test
     */
    public function test()
    {
        // given
        $readMeExample = "/**
 * The description of foo. This function does a lot of thing
 *   which are described here.
 *
 * Some more text here.
 *
 * @important
 * @uses FooReader
 * @internal 
 * @param string|callable \$first   This is the first param
 * @param int             \$second  The second one
 * @return void
 * @throws InvalidArgumentException
 * @throws DomainException if first argument is not found
 */";
        $parser = new PHPDocParser(PhpDocumentor::tags()->with([
            new SummaryAndDescription(),
            new FlagTag('important')
        ]));
        // when
        $meta = $parser->parse($readMeExample);
        // then
        $expected = [
            'important' => true,
            'uses' => [
                'type' => 'FooReader'
            ],
            'internal' => true,
            'params' => [
                'first' => [
                    'type' => "string|callable",
                    'name' => "first",
                    'description' => "This is the first param"
                ],
                'second' => [
                    'type' => "int",
                    'name' => "second",
                    'description' => 'The second one',
                ]
            ],
            'return' => [
                'type' => 'void'
            ],
            'throws' => [
                ['type' => 'InvalidArgumentException'],
                ['type' => 'DomainException', 'description' => 'if first argument is not found'],
            ],
            'summary' => "The description of foo. This function does a lot of thing",
            'description' => "The description of foo. This function does a lot of thing
which are described here.
Some more text here.",
        ];
        $this->assertSame($expected, $meta);
    }
}
