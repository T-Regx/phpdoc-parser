<?php
namespace Test\Feature\tags\method;

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
        $this->assertParses(resource('tags/method.many.txt'), [
            'methods' => [
                [
                    'name'        => 'getString',
                    'return_type' => 'string',
                    'params'      => [],
                ],
                [
                    'name'        => 'setInteger',
                    'return_type' => 'void',
                    'params'      => ['integer' => ['name' => 'integer', 'type' => 'int']]
                ],
                [
                    'name'        => 'setString',
                    'params' => ['integer' => ['name' => 'integer', 'type' => 'int']]
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
        $this->assertParses(resource('tags/method.duplicate.txt'), [
            'methods' => [
                [
                    'name'        => 'setString',
                    'params'      => [],
                ],
                [
                    'name'        => 'setString',
                    'params' => ['argument' => ['name' => 'argument', 'type' => 'int']]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseMethodStatic()
    {
        // when, then
        $this->assertParses(resource('tags/method.static.txt'), [
            'methods' => [
                [
                    'name'        => 'staticGetter',
                    'return_type' => 'string',
                    'params'      => []
                ]
            ]
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
                    'name'        => 'someMethod',
                    'return_type' => 'string',
                    'params'      => [
                        'one'   => [
                            'name' => 'one'
                        ],
                        'two'   => [
                            'name' => 'two',
                            'type' => 'Foo',
                        ],
                        'three' => [
                            'name'    => 'three',
                            'type'    => 'int',
                            'default' => '12'
                        ],
                        'four'  => [
                            'name'    => 'four',
                            'type'    => 'string',
                            'default' => 'bar'
                        ],
                        'five'  => [
                            'name'    => 'five',
                            'type'    => 'string',
                            'default' => 'zoo'
                        ],
                        'six'   => [
                            'name'    => 'six',
                            'type'    => 'array',
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
                    'name'        => 'someMethod',
                    'return_type' => 'any_namespace\Zoo',
                    'params'      => [
                        'one'   => [
                            'name' => 'one'
                        ],
                        'two'   => [
                            'name' => 'two',
                            'type' => 'any_namespace\Foo',
                        ],
                        'three' => [
                            'name'    => 'three',
                            'type'    => 'any_namespace\Bar\Bars',
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
