<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\MapTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\MapTag
 * @covers \Jasny\PhpdocParser\Tag\AbstractArrayTag
 */
class MapTagTest extends TestCase
{
    /**
     * @test
     */
    public function testGetName()
    {
        // given
        $tag = new MapTag('foo');
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    /**
     * @test
     */
    public function testGetTypeDefault()
    {
        // given
        $tag = new MapTag('foo');
        // when, then
        $this->assertEquals('string', $tag->getType());
    }

    /**
     * @test
     * @dataProvider typeProvider
     */
    public function testGetType(string $type)
    {
        $tag = new MapTag('foo', $type);
        $this->assertEquals($type, $tag->getType());
    }

    public function typeProvider(): array
    {
        return [['string'], ['int'], ['float']];
    }

    /**
     * @test
     */
    public function testGetTypeInvalid()
    {
        // then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid type 'ton'");
        // when
        new MapTag('foo', 'ton');
    }

    public function testProcess()
    {
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process(['bar' => 1], 'red=good, green = better, blue =  best');
        // then
        $this->assertEquals(['bar' => 1, 'foo' => ['red' => 'good', 'green' => 'better', 'blue' => 'best']], $result);
    }

    public function testProcessParenthesis()
    {
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process([], '(red=good, green=better, blue=best) to be ignored');
        // then
        $this->assertEquals(['foo' => ['red' => 'good', 'green' => 'better', 'blue' => 'best']], $result);
    }

    public function testProcessEmpty()
    {
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process([], '');
        // then
        $this->assertEquals(['foo' => []], $result);
    }

    public function testProcessQuoted()
    {
        $value = 'one="hello, world", "t w o"=greetings, \'three\'=\'bye, bye\', four = this is "also = quoted",'
            . 'five=o\'reilly, six=o\'kay';

        $tag = new MapTag('foo');

        $result = $tag->process([], $value);

        $expected = ['one' => 'hello, world', 't w o' => 'greetings', 'three' => 'bye, bye',
            'four' => 'this is "also = quoted"', 'five' => 'o\'reilly', 'six' => 'o\'kay'];
        $this->assertEquals(['foo' => $expected], $result);
    }

    public function testProcessQuotedParenthesis()
    {
        $value = '(iam="not (here)", one = two)';
        $tag = new MapTag('foo');

        $result = $tag->process([], $value);
        $this->assertEquals(['foo' => ['iam' => 'not (here)', 'one' => 'two']], $result);
    }

    public function testProcessSkip()
    {
        $tag = new MapTag('foo');

        $result = $tag->process([], 'start=hi,middle=,next = ,end=bye');
        $this->assertSame(['foo' => ['start' => 'hi', 'middle' => '', 'next' => '', 'end' => 'bye']], $result);
    }

    public function testProcessString()
    {
        $tag = new MapTag('foo', 'string');

        $result = $tag->process([], '1=hi, 2=42, 3=bye');
        $this->assertSame(['foo' => [1 => 'hi', 2 => '42', 3 => 'bye']], $result);
    }

    public function testProcessAssocInt()
    {
        $tag = new MapTag('foo', 'int');

        $result = $tag->process([], 'red = 66, green = 229, blue = 244');
        $this->assertEquals(['foo' => ['red' => 66, 'green' => 229, 'blue' => 244]], $result);
    }

    public function testProcessAssocFloat()
    {
        $tag = new MapTag('foo', 'float');

        $result = $tag->process([], 'a = 3.14, b = 7, c = 10e4, d = 1.41429, e = -1.2');
        $this->assertEquals(['foo' => ['a' => 3.14, 'b' => 7.0, 'c' => 10e4, 'd' => 1.41429, 'e' => -1.2]], $result);
    }

    public function testProcessInvalidNoKey()
    {
        $tag = new MapTag('foo');

        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo red = 66, green, blue = 244': no key for value 'green'");

        $tag->process([], 'red = 66, green, blue = 244');
    }

    public function testProcessInvalidBlankKey()
    {
        $tag = new MapTag('foo');

        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo red = 66, =229, blue = 244': no key for value '229'");

        $tag->process([], 'red = 66, =229, blue = 244');
    }

    public function testProcessInvalidInt()
    {
        $tag = new MapTag('foo', 'int');

        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo a = 10, b = 33.2, c = 20': invalid value '33.2'");

        $tag->process([], 'a = 10, b = 33.2, c = 20');
    }

    public function testProcessInvalidFloat()
    {
        $tag = new MapTag('foo', 'float');

        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo a = 10, b = 33.., c = 20': invalid value '33..'");

        $tag->process([], 'a = 10, b = 33.., c = 20');
    }

    public function testProcessInvalidType()
    {
        $tag = new MapTag('foo');
        $tag->type = 'abc';

        $this->expectException(\UnexpectedValueException::class);

        $tag->process([], 'a = 10');
    }
}
