<?php
namespace Test\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\RegExpTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\RegExpTag
 */
class RegExpTagTest extends TestCase
{
    /**
     * @test
     */
    public function testGetRegExp()
    {
        // given
        $tag = new RegExpTag('foo', 'foo_regexp');
        // when
        $result = $tag->getRegExp();
        // then
        $this->assertSame('foo_regexp', $result);
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess($regexp, $value, $expected)
    {
        $tag = new RegExpTag('foo', $regexp);
        $result = $tag->process(['some' => 'value'], $value);

        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        return [
            ['//', 'foo string to parse', ['some' => 'value', 'foo' => ['']]],
            ['/.*/', 'foo string to parse', ['some' => 'value', 'foo' => ['foo string to parse']]],
            ['/\s+(\S+)/', 'foo string to parse', ['some' => 'value', 'foo' => [' string', 'string']]],
        ];
    }

    /**
     * @test
     */
    public function testProcessException()
    {
        // given
        $tag = new RegExpTag('foo', '/^abc/');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@foo not-abc': invalid syntax");
        // when
        $tag->process(['some' => 'value'], 'not-abc');
    }
}
