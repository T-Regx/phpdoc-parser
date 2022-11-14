<?php

namespace Jasny\PhpdocParser\Tests;

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\Tag;
use Jasny\PhpdocParser\TagSet;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\PhpdocParser
 */
class PhpdocParserTest extends TestCase
{
    /**
     * @var Tag[]|MockObject[]
     */
    protected $tags;

    /**
     * @var PhpdocParser
     */
    protected $parser;

    public function setUp(): void
    {
        $tags = [
            'foo' => $this->createConfiguredMock(Tag::class, ['getName' => 'foo']),
            'bar' => $this->createConfiguredMock(Tag::class, ['getName' => 'bar']),
            'qux' => $this->createConfiguredMock(Tag::class, ['getName' => 'qux']),
        ];

        $tagset = $this->createMock(TagSet::class);
        $tagset->expects($this->any())->method('offsetExists')->willReturnCallback(function ($key) use ($tags) {
            return isset($tags[$key]);
        });
        $tagset->expects($this->any())->method('offsetGet')->willReturnCallback(function ($key) use ($tags) {
            if (!isset($tags[$key])) {
                throw new \OutOfRangeException("Unknown tag '@{$key}'");
            }
            return $tags[$key];
        });

        $this->tags = $tags;
        $this->parser = new PhpdocParser($tagset);
    }

    public function testParseFlag()
    {
        $this->tags['foo']->expects($this->once())->method('process')
            ->with([], '')->willReturn(['foo' => true]);

        $doc = <<<DOC
/**
 * @foo
 */
DOC;

        $result = $this->parser->parse($doc);

        $this->assertEquals(['foo' => true], $result);
    }

    public function testParseFlagSeveral()
    {
        $this->tags['foo']->expects($this->once())->method('process')
            ->with([], '')->willReturn(['foo' => true]);

        $this->tags['bar']->expects($this->once())->method('process')
            ->with(['foo' => true], '')->willReturn(['foo' => true, 'bar' => true]);

        $doc = <<<DOC
/**
 * @foo
 * @bar
 */
DOC;

        $result = $this->parser->parse($doc);

        $this->assertEquals(['foo' => true, 'bar' => true], $result);
    }

    public function testParseValue()
    {
        $this->tags['foo']->expects($this->once())->method('process')
            ->with([], 'hello')->willReturn(['foo' => 'HELLO!']);

        $doc = <<<DOC
/**
 * @foo hello
 */
DOC;

        $result = $this->parser->parse($doc);

        $this->assertSame(['foo' => 'HELLO!'], $result);
    }

    public function testParseMultiple()
    {
        $this->tags['foo']->expects($this->exactly(3))->method('process')
            ->withConsecutive([[], ''], [['foo' => 1], ''], [['foo' => 2], ''])
            ->willReturnOnConsecutiveCalls(['foo' => 1], ['foo' => 2], ['foo' => 3]);

        $doc = <<<DOC
/**
 * @foo
 * @foo
 * @foo
 */
DOC;

        $result = $this->parser->parse($doc);

        $this->assertSame(['foo' => 3], $result);
    }

    public function testParseFull()
    {
        $this->tags['foo']->expects($this->exactly(2))->method('process')
            ->withConsecutive([[], 'hi'], [['foo' => ['hi'], 'bar' => true], 'bye'])
            ->willReturnOnConsecutiveCalls(['foo' => ['hi']], ['foo' => ['hi', 'bye'], 'bar' => true]);

        $this->tags['bar']->expects($this->once())->method('process')
            ->with(['foo' => ['hi']], '')->willReturn(['foo' => ['hi'], 'bar' => true]);

        $doc = <<<DOC
/**
 * This should be ignored, so should {@qux this}
 *
 * @foo hi
 * @bar
 * @foo bye
 * @ign
 */
DOC;

        $result = $this->parser->parse($doc);

        $this->assertEquals(['foo' => ['hi', 'bye'], 'bar' => true], $result);
    }

    /**
     * Test using summery tag
     */
    public function testSummery()
    {
        $doc = <<<DOC
/**
 * Some summery
 *
 * General description
 * spanning a few lines
 * of doc-comment.
 *
 * @bar
 * @foo bye
 * @ign
 */
DOC;

        $expected = ['summery' => 'Some summery', 'description' => "Some summery\nGeneral description\nspanning a few lines\nof doc-comment."];

        $tags = [
            'summery' => $this->createConfiguredMock(Tag::class, ['getName' => 'summery']),
        ];

        $tagset = $this->createMock(TagSet::class);
        $tagset->expects($this->any())->method('offsetExists')->willReturnCallback(function ($key) use ($tags) {
            return isset($tags[$key]);
        });

        $tagset->expects($this->any())->method('offsetGet')->willReturnCallback(function ($key) use ($tags) {
            return $tags[$key];
        });

        $tags['summery']->expects($this->once())->method('process')->with([], $doc)->willReturn($expected);

        $parser = new PhpdocParser($tagset);
        $result = $parser->parse($doc);

        $this->assertSame($expected, $result);
    }

    /**
     * Test using callback after parsing
     */
    public function testCallback()
    {
        $doc = "/**\n * @bar Some value\n */";

        $expected = ['value after callback'];

        $this->tags['bar']
            ->expects($this->once())
            ->method('process')
            ->with([], 'Some value')
            ->willReturn(['bar' => 'Some value']);

        $callback = function ($argument) use ($expected) {
            Assert::assertSame(['bar' => 'Some value'], $argument);
            return $expected;
        };
        // when
        $result = $this->parser->parse($doc, $callback);
        // then
        $this->assertSame($expected, $result);
    }

    /**
     * Test processing multiline tags
     */
    public function testMultiline()
    {
        $doc = <<<DOC
/**
 * Summery should be ignored.
 *
 * General description
 *  spanning a few lines
 *  of doc-comment. Should be ignored.
 *
 * @bar Some
 *  bar value.
 * @foo This one
 *  also has multiline
 *  value.
 * @qux Single line value
 */
DOC;
        $expected = [
            'bar' => 'Some bar value.',
            'foo' => 'This one also has multiline value.',
            'qux' => 'Single line value'
        ];

        $this->tags['bar']->expects($this->once())->method('process')->with([], 'Some bar value.')->willReturn(['bar' => 'Some bar value.']);
        $this->tags['foo']->expects($this->once())->method('process')->with(['bar' => 'Some bar value.'], 'This one also has multiline value.')->willReturn([
            'bar' => 'Some bar value.',
            'foo' => 'This one also has multiline value.'
        ]);
        $this->tags['qux']->expects($this->once())->method('process')->with([
            'bar' => 'Some bar value.',
            'foo' => 'This one also has multiline value.'
        ], 'Single line value')->willReturn($expected);

        $result = $this->parser->parse($doc);

        $this->assertSame($expected, $result);
    }

    /**
     * Test parsing comment with no tags
     */
    public function test()
    {
        $doc = <<<DOC
/**
 * Just some description. Should be ignored.
 */
DOC;

        $this->tags['bar']->expects($this->never())->method('process');
        $this->tags['foo']->expects($this->never())->method('process');
        $this->tags['qux']->expects($this->never())->method('process');

        $result = $this->parser->parse($doc);

        $this->assertSame([], $result);
    }
}
