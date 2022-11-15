<?php
namespace Test\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\PhpDocumentor\TypeTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\PhpDocumentor\TypeTag
 */
class TypeTagTest extends TestCase
{
    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess(string $value, ?callable $fqsenConvertor, array $expected)
    {
        $tag = new TypeTag('foo', $fqsenConvertor);
        $result = $tag->process(['some' => 'value'], $value);

        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        return [
            [
                'FooType',
                null,
                ['some' => 'value', 'foo' => ['type' => 'FooType']]
            ],
            [
                'FooType Some description here',
                null,
                ['some' => 'value', 'foo' => ['type' => 'FooType', 'description' => 'Some description here']]
            ],
            [
                'Bar\\Foo\\Type',
                null,
                ['some' => 'value', 'foo' => ['type' => 'Bar\\Foo\\Type']]
            ],
            [
                'Bar\\Foo\\Type Some description here',
                null,
                ['some' => 'value', 'foo' => ['type' => 'Bar\\Foo\\Type', 'description' => 'Some description here']]
            ],
            [
                'FooType Some description here',
                function ($class) {
                    return 'Bar\\' . $class;
                },
                ['some' => 'value', 'foo' => ['type' => 'Bar\\FooType', 'description' => 'Some description here']]
            ],
        ];
    }

    /**
     * @test
     */
    public function testProcessEmptyValue()
    {
        // given
        $tag = new TypeTag('foo');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessageMatches("/Failed to parse '@foo': tag value should not be empty/");
        // when
        $tag->process(['some' => 'value'], '');
    }
}
