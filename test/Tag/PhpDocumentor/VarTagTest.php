<?php
namespace Test\Tag\PhpDocumentor;

use Jasny\PhpdocParser\Tag\PhpDocumentor\VarTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\PhpDocumentor\VarTag
 */
class VarTagTest extends TestCase
{
    /**
     * @test
     */
    public function testGetAdditionalProperties()
    {
        // given
        $tag = new VarTag('foo', null, ['bar' => 'baz']);
        // when
        $result = $tag->getAdditionalProperties();
        // then
        $this->assertSame(['bar' => 'baz'], $result);
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess(string $value, ?callable $fqsenConvertor, array $additional, array $expected)
    {
        $tag = new VarTag('foo', $fqsenConvertor, $additional);
        $result = $tag->process(['some' => 'value'], $value);

        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        return [
            [
                'int Some description here',
                null,
                [],
                [
                    'some' => 'value',
                    'foo' => [
                        'type' => 'int',
                        'description' => 'Some description here'
                    ]
                ]
            ],
            [
                '$amount Some description here',
                null,
                [],
                [
                    'some' => 'value',
                    'foo' => [
                        'name' => 'amount',
                        'description' => 'Some description here'
                    ]
                ]
            ],
            [
                'int $amount "some id"',
                null,
                [],
                [
                    'some' => 'value',
                    'foo' => [
                        'type' => 'int',
                        'name' => 'amount',
                        'id' => 'some id'
                    ]
                ]
            ],
            [
                'int|string|Foo\\Bar|null $amount "some id"',
                null,
                ['any' => 'adds', 'little' => 'rats'],
                [
                    'some' => 'value',
                    'foo' => [
                        'type' => 'int|string|Foo\\Bar|null',
                        'name' => 'amount',
                        'id' => 'some id',
                        'any' => 'adds',
                        'little' => 'rats'
                    ]
                ]
            ],
            [
                'Foo $amount "some id" Some description here',
                function ($class) {
                    return 'some_namespace\\' . $class;
                },
                ['any' => 'adds', 'little' => 'rats'],
                [
                    'some' => 'value',
                    'foo' => [
                        'type' => 'some_namespace\\Foo',
                        'name' => 'amount',
                        'id' => 'some id',
                        'description' => 'Some description here',
                        'any' => 'adds',
                        'little' => 'rats'
                    ]
                ]
            ],
        ];
    }
}
