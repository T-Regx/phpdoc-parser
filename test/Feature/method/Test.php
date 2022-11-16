<?php
namespace Test\Feature\method;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\Functions;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseMethods()
    {
        // when, then
        $this->assertParses(resource('method.many.txt'), [
            'methods' => [
                [
                    'return_type' => 'string',
                    'name'        => 'getString',
                    'params'      => [],
                ],
                [
                    'return_type' => 'void',
                    'name'        => 'setInteger',
                    'params'      => ['integer' => ['type' => 'int', 'name' => 'integer']]
                ],
                [
                    'return_type' => '',
                    'name'        => 'setString',
                    'params'      => ['integer' => ['type' => 'int', 'name' => 'integer']]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseMethodsDuplicate()
    {
        // when, then
        $this->assertParses(resource('method.duplicate.txt'), [
            'methods' => [
                [
                    'return_type' => '',
                    'name'        => 'setString',
                    'params'      => [],
                ],
                [
                    'return_type' => '',
                    'name'        => 'setString',
                    'params'      => ['argument' => ['type' => 'int', 'name' => 'argument']]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseMethodStatic()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('method.static.txt'), [
            'link' => 'https://example.com/my/bar'
        ]);
    }

    /**
     * @test
     * @dataProvider notations
     */
    public function shouldFailToParseMalformedMethod(string $value)
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@method $value': invalid syntax");
        // when
        $parser->parse("/** @method $value\n */");
    }

    public function notations(): array
    {
        return [
            ['Zoo $someMethod($one, Foo $two, Bar\\Bars $three = null) Some method description here'],
            ['Zoo someMethod(one, Foo $two, Bar\\Bars $three = test) Some method description here'],
        ];
    }

    /**
     * @test
     */
    public function test()
    {
        // given
        $docBlock = 'string someMethod($one, Foo $two, int $three = 12, string $four = "bar", string $five = \'zoo\', array $six = ["test"]) Some method description here';
        // when, then
        $this->assertParses("/** @method $docBlock\n*/", [
            'methods' => [
                [
                    'return_type' => 'string',
                    'name'        => 'someMethod',
                    'params'      => [
                        'one'   => [
                            'name' => 'one'
                        ],
                        'two'   => [
                            'type' => 'Foo',
                            'name' => 'two',
                        ],
                        'three' => [
                            'type'    => 'int',
                            'name'    => 'three',
                            'default' => '12'
                        ],
                        'four'  => [
                            'type'    => 'string',
                            'name'    => 'four',
                            'default' => 'bar'
                        ],
                        'five'  => [
                            'type'    => 'string',
                            'name'    => 'five',
                            'default' => 'zoo'
                        ],
                        'six'   => [
                            'type'    => 'array',
                            'name'    => 'six',
                            'default' => '["test"]'
                        ],
                    ],
                    'description' => 'Some method description here'
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldMapClassname()
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags(Functions::prepend('any_namespace\\')));
        $docBlock = 'Zoo someMethod($one, Foo $two, Bar\Bars $three = null) Some method description here';
        // when, then
        $parsed = $parser->parse("/** @method $docBlock\n*/");
        // then
        $expected = [
            'methods' => [
                [
                    'return_type' => 'any_namespace\Zoo',
                    'name'        => 'someMethod',
                    'params'      => [
                        'one'   => [
                            'name' => 'one'
                        ],
                        'two'   => [
                            'type' => 'any_namespace\Foo',
                            'name' => 'two',
                        ],
                        'three' => [
                            'type'    => 'any_namespace\Bar\Bars',
                            'name'    => 'three',
                            'default' => 'null'
                        ]
                    ],
                    'description' => 'Some method description here'
                ]
            ]
        ];
        $this->assertSame($expected, $parsed);
    }
}
