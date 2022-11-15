<?php
namespace Test\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\ArrayTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\ArrayTag
 * @covers \Jasny\PhpdocParser\Tag\AbstractArrayTag
 */
class ArrayTagTest extends TestCase
{
    /**
     * @dataProvider typeProvider
     */
    public function testGetType(string $type)
    {
        // given
        $tag = new ArrayTag('foo', $type);
        // when, then
        $this->assertEquals($type, $tag->getType());
    }

    public function typeProvider(): array
    {
        return [
            ['string'],
            ['int'],
            ['float']
        ];
    }

    public function testGetTypeInvalid()
    {
        // then
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid type 'ton'");
        // when
        new ArrayTag('foo', 'ton');
    }

    public function testProcess()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process(['bar' => true], 'red, green, blue');
        // then
        $this->assertEquals(['bar' => true, 'foo' => ['red', 'green', 'blue']], $result);
    }

    public function testProcessParenthesis()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process(['bar' => true], '(red, green, blue) to be ignored');
        // then
        $this->assertEquals(['bar' => true, 'foo' => ['red', 'green', 'blue']], $result);
    }

    public function testProcessEmpty()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process([], '');
        // then
        $this->assertEquals(['foo' => []], $result);
    }

    public function testProcessQuoted()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process([], '"hello, world", greetings, \'bye, bye\', this is "also, quoted", o\'reilly, o\'kay');
        // then
        $expected = ['hello, world', 'greetings', 'bye, bye', 'this is "also, quoted"', 'o\'reilly', 'o\'kay'];
        $this->assertEquals(['foo' => $expected], $result);
    }

    public function testProcessQuotedParenthesis()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process([], '("not (here)", one = two, greetings)');
        // then
        $this->assertEquals(['foo' => ['not (here)', 'one = two', 'greetings']], $result);
    }

    public function testProcessSkip()
    {
        // given
        $tag = new ArrayTag('foo');
        // when
        $result = $tag->process([], 'hi, , bye');
        // then
        $this->assertSame(['foo' => ['hi', '', 'bye']], $result);
    }

    public function testProcessString()
    {
        // given
        $tag = new ArrayTag('foo', 'string');
        // when
        $result = $tag->process([], 'hi, 42, bye');
        // then
        $this->assertSame(['foo' => ['hi', '42', 'bye']], $result);
    }

    public function testProcessInt()
    {
        // given
        $tag = new ArrayTag('foo', 'int');
        // when
        $result = $tag->process([], '3, 5, 11, 17, 31, -1, +2');
        // then
        $this->assertSame(['foo' => [3, 5, 11, 17, 31, -1, 2]], $result);
    }

    public function testProcessFloat()
    {
        // given
        $tag = new ArrayTag('foo', 'float');
        // when
        $result = $tag->process([], '3.14, 7, 10e4, 1.41429, -1.2');
        // then
        $this->assertSame(['foo' => [3.14, 7.0, 10e4, 1.41429, -1.2]], $result);
    }

    public function testProcessInvalidInt()
    {
        // given
        $tag = new ArrayTag('foo', 'int');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo 10, 33.2, 20': invalid value '33.2'");
        // when
        $tag->process([], '10, 33.2, 20');
    }

    public function testProcessInvalidFloat()
    {
        // given
        $tag = new ArrayTag('foo', 'float');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo 10, 33.., 20': invalid value '33..'");
        // when
        $tag->process([], '10, 33.., 20');
    }

    public function testProcessInvalidType()
    {
        // given
        $tag = new ArrayTag('foo');
        $tag->type = 'abc';
        // then
        $this->expectException(\UnexpectedValueException::class);
        // when
        $tag->process([], 'a');
    }
}
