<?php
namespace Test\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\PhpDocumentor\MethodTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\PhpDocumentor\MethodTag
 */
class MethodTagTest extends TestCase
{
    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess(string $value, ?callable $fqsenConvertor, array $expected)
    {
        // given
        $tag = new MethodTag('foo', $fqsenConvertor);
        // when
        $result = $tag->process(['some' => 'value'], $value);
        // then
        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'process' method
     *
     * @return array
     */
    public function processProvider(): array
    {
        return [
            [
                'string someMethod($one, Foo $two, int $three = 12, string $four = "bar", string $five = \'zoo\', array $six = ["test"]) Some method description here',
                null,
                [
                    'some' => 'value',
                    'foo' => [
                        'return_type' => 'string',
                        'name' => 'someMethod',
                        'params' => [
                            'one' => [
                                'name' => 'one'
                            ],
                            'two' => [
                                'type' => 'Foo',
                                'name' => 'two',
                            ],
                            'three' => [
                                'type' => 'int',
                                'name' => 'three',
                                'default' => '12'
                            ],
                            'four' => [
                                'type' => 'string',
                                'name' => 'four',
                                'default' => 'bar'
                            ],
                            'five' => [
                                'type' => 'string',
                                'name' => 'five',
                                'default' => 'zoo'
                            ],
                            'six' => [
                                'type' => 'array',
                                'name' => 'six',
                                'default' => '["test"]'
                            ],
                        ],
                        'description' => 'Some method description here'
                    ]
                ]
            ],
            [
                'Zoo someMethod($one, Foo $two, Bar\\Bars $three = null) Some method description here',
                function ($class) {
                    return 'any_namespace\\' . $class;
                },
                [
                    'some' => 'value',
                    'foo' => [
                        'return_type' => 'any_namespace\\Zoo',
                        'name' => 'someMethod',
                        'params' => [
                            'one' => [
                                'name' => 'one'
                            ],
                            'two' => [
                                'type' => 'any_namespace\\Foo',
                                'name' => 'two',
                            ],
                            'three' => [
                                'type' => 'any_namespace\\Bar\\Bars',
                                'name' => 'three',
                                'default' => 'null'
                            ]
                        ],
                        'description' => 'Some method description here'
                    ]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider processExceptionProvider
     */
    public function testProcessException(string $value)
    {
        // given
        $tag = new MethodTag('foo');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessageMatches("/Failed to parse '@foo .*?': invalid syntax/");
        // when
        $tag->process(['some' => 'value'], $value);
    }

    public function processExceptionProvider(): array
    {
        return [
            ['Zoo $someMethod($one, Foo $two, Bar\\Bars $three = null) Some method description here'],
            ['Zoo someMethod(one, Foo $two, Bar\\Bars $three = test) Some method description here'],
        ];
    }
}
