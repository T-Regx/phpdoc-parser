<?php

namespace Test\Tag;

use Jasny\PhpdocParser\Tag\WordTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\WordTag
 */
class WordTagTest extends TestCase
{
    public function testGetDefault()
    {
        // given
        $tag = new WordTag('foo', true);
        // when, then
        $this->assertSame(true, $tag->getDefault());
    }

    public function testGetDefaultUnspecified()
    {
        // given
        $tag = new WordTag('foo');
        // when, then
        $this->assertSame('', $tag->getDefault());
    }

    public function testGetName()
    {
        // given
        $tag = new WordTag('foo');
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testProcess()
    {
        // given
        $tag = new WordTag('foo', true);
        // when
        $result = $tag->process(['bar' => 1], 'hi');
        // then
        $this->assertEquals(['bar' => 1, 'foo' => 'hi'], $result);
    }

    public function testProcessDefault()
    {
        // given
        $tag = new WordTag('foo', true);
        // when
        $result = $tag->process([], '');
        // then
        $this->assertEquals(['foo' => true], $result);
    }

    public function testProcessSentence()
    {
        // given
        $tag = new WordTag('foo');
        // when
        $result = $tag->process([], 'hello sweet world');
        // then
        $this->assertEquals(['foo' => 'hello'], $result);
    }

    /**
     * @dataProvider quotedWords
     */
    public function testProcessQuote($value)
    {
        // given
        $tag = new WordTag('foo');
        // when
        $result = $tag->process([], $value);
        // then
        $this->assertEquals(['foo' => 'hello world'], $result);
    }

    public function quotedWords(): array
    {
        return [
            ['"hello world" This a sentence.'],
            ['\'hello world\' This a sentence.']
        ];
    }
}
