<?php
namespace Test\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\NumberTag;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @covers \Jasny\PhpdocParser\Tag\NumberTag
 */
class NumberTagTest extends TestCase
{
    /**
     * @test
     * @dataProvider constructProvider
     */
    public function testConstruct(string $type, $min, $max)
    {
        // given
        $tag = new NumberTag('foo', $type, $min, $max);
        // then
        $this->assertSame('foo', $tag->getName());
        $this->assertSame($type, $tag->type);
        $this->assertSame($min, $tag->min);
        $this->assertSame($max, $tag->max);
    }

    public function constructProvider(): array
    {
        return [
            ['int', 0, 0],
            ['int', 0, 5],
            ['int', 0, INF],
            ['int', -INF, INF],
            ['int', -INF, 0],
            ['int', -INF, 10],
            ['integer', 0, 0],
            ['integer', 0, 5],
            ['integer', 0, INF],
            ['integer', -INF, INF],
            ['integer', -INF, 0],
            ['integer', -INF, 10],
            ['float', 0, 0],
            ['float', 0, 5],
            ['float', 0, INF],
            ['float', -INF, INF],
            ['float', -INF, 0],
            ['float', -INF, 10],
            ['float', 2.5, 10.7]
        ];
    }

    /**
     * @test
     * @dataProvider constructExceptionProvider
     */
    public function testConstructException(string $type, $min, $max, string $exceptionClass, string $exceptionMessage)
    {
        // then
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        // when
        new NumberTag('foo', $type, $min, $max);
    }

    public function constructExceptionProvider(): array
    {
        return [
            ['string', 0, 1, PhpdocException::class, 'NumberTag should be of type int or float, string given'],
            ['int', '0', 1, TypeError::class, 'Expected int or float, string given'],
            ['int', 0, [], TypeError::class, 'Expected int or float, array given'],
            ['float', 3, 1, PhpdocException::class, 'Min value (given 3) should be less than max (given 1)'],
        ];
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess(string $type, string $value, array $expected)
    {
        // given
        $tag = new NumberTag('foo', $type, -10);
        // when
        $result = $tag->process(['some' => 'value'], $value);
        // then
        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        return [
            ['int', '2 is a big number', ['some' => 'value', 'foo' => 2]],
            ['int', '2.53 is a big number', ['some' => 'value', 'foo' => 2]],
            ['float', '2.53 is a big number', ['some' => 'value', 'foo' => 2.53]],
            ['float', '+2.53 is a big number', ['some' => 'value', 'foo' => 2.53]],
            ['float', '-2.53 is a big number', ['some' => 'value', 'foo' => -2.53]],
        ];
    }

    /**
     * @test
     * @dataProvider processExceptionProvider
     */
    public function testProcessException(string $type, $min, $max, string $value, string $exceptionMessage)
    {
        // given
        $tag = new NumberTag('foo', $type, $min, $max);
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage($exceptionMessage);
        // when
        $tag->process(['some' => 'value'], $value);
    }

    public function processExceptionProvider(): array
    {
        return [
            ['int', 0, INF, '"2" is a big number', "Failed to parse '@foo \"2\"': not a number"],
            ['int', 0, INF, '2-and-half is a big number', "Failed to parse '@foo 2-and-half': not a number"],
            ['int', 0, INF, 'two is a big number', "Failed to parse '@foo two': not a number"],
            ['int', 0, 3, '4 is a big number', "Parsed value 4 should be less then max value 3"],
            ['int', 0, 3, '-1 is a big number', "Parsed value -1 should be greater then min value 0"],
        ];
    }
}
