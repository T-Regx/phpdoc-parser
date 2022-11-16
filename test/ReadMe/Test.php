<?php
namespace Test\ReadMe;

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use Jasny\PhpdocParser\Tag\FlagTag;
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
        $parser = new PHPDocParser(PhpDocumentor::tags()->with([new FlagTag('important')]));
        // when
        $meta = $parser->parse($readMeExample);
        // then
        $expected = [
            'summary'     => 'The description of foo.',
            'description' => "This function does a lot of thing\nwhich are described here.\n\nSome more text here.",
            'important'   => true,
            'uses'        => [
                'type' => 'FooReader'
            ],
            'internal'    => true,
            'params'      => [
                [
                    'name'        => "first",
                    'type'        => "string|callable",
                    'description' => "This is the first param"
                ],
                [
                    'name'        => "second",
                    'type'        => "int",
                    'description' => 'The second one',
                ]
            ],
            'return'      => [
                'type' => 'void'
            ],
            'throws'      => [
                ['type' => 'InvalidArgumentException'],
                ['type' => 'DomainException', 'description' => 'if first argument is not found'],
            ],
        ];
        $this->assertSame($expected, $meta);
    }
}
