<?php
namespace Test\Tag;

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
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process([], "one=\"hello, world\", \"t w o\"=greetings, 'three'='bye, bye', four = this is \"also = quoted\",five=o'reilly, six=o'kay");
        // then
        $expected = [
            'one' => 'hello, world',
            't w o' => 'greetings',
            'three' => 'bye, bye',
            'four' => 'this is "also = quoted"',
            'five' => 'o\'reilly',
            'six' => 'o\'kay'
        ];
        $this->assertEquals(['foo' => $expected], $result);
    }

    public function testProcessQuotedParenthesis()
    {
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process([], '(iam="not (here)", one = two)');
        // then
        $this->assertEquals(['foo' => ['iam' => 'not (here)', 'one' => 'two']], $result);
    }

    public function testProcessSkip()
    {
        // given
        $tag = new MapTag('foo');
        // when
        $result = $tag->process([], 'start=hi,middle=,next = ,end=bye');
        // then
        $this->assertSame(['foo' => ['start' => 'hi', 'middle' => '', 'next' => '', 'end' => 'bye']], $result);
    }

    public function testProcessString()
    {
        // given
        $tag = new MapTag('foo', 'string');
        // when
        $result = $tag->process([], '1=hi, 2=42, 3=bye');
        // then
        $this->assertSame(['foo' => [1 => 'hi', 2 => '42', 3 => 'bye']], $result);
    }

    public function testProcessAssocInt()
    {
        // given
        $tag = new MapTag('foo', 'int');
        // when
        $result = $tag->process([], 'red = 66, green = 229, blue = 244');
        // then
        $this->assertEquals(['foo' => ['red' => 66, 'green' => 229, 'blue' => 244]], $result);
    }

    public function testProcessAssocFloat()
    {
        // given
        $tag = new MapTag('foo', 'float');
        // when
        $result = $tag->process([], 'a = 3.14, b = 7, c = 10e4, d = 1.41429, e = -1.2');
        // then
        $this->assertEquals(['foo' => ['a' => 3.14, 'b' => 7.0, 'c' => 10e4, 'd' => 1.41429, 'e' => -1.2]], $result);
    }

    public function testProcessInvalidNoKey()
    {
        // given
        $tag = new MapTag('foo');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo red = 66, green, blue = 244': no key for value 'green'");
        // when
        $tag->process([], 'red = 66, green, blue = 244');
    }

    public function testProcessInvalidBlankKey()
    {
        // given
        $tag = new MapTag('foo');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo red = 66, =229, blue = 244': no key for value '229'");
        // when
        $tag->process([], 'red = 66, =229, blue = 244');
    }

    public function testProcessInvalidInt()
    {
        // given
        $tag = new MapTag('foo', 'int');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo a = 10, b = 33.2, c = 20': invalid value '33.2'");
        // when
        $tag->process([], 'a = 10, b = 33.2, c = 20');
    }

    public function testProcessInvalidFloat()
    {
        // given
        $tag = new MapTag('foo', 'float');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo a = 10, b = 33.., c = 20': invalid value '33..'");
        // when
        $tag->process([], 'a = 10, b = 33.., c = 20');
    }

    public function testProcessInvalidType()
    {
        // given
        $tag = new MapTag('foo');
        $tag->type = 'abc';
        // then
        $this->expectException(\UnexpectedValueException::class);
        // when
        $tag->process([], 'a = 10');
    }
}
