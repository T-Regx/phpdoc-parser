<?php

namespace Jasny\PhpdocParser\Tests;

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\TagSet;
use Jasny\PhpdocParser\Tests\Fakes\ConsecutiveProcessTag;
use Jasny\PhpdocParser\Tests\Fakes\ConstantNameTag;
use Jasny\PhpdocParser\Tests\Fakes\EmptyNotationsTag;
use Jasny\PhpdocParser\Tests\Fakes\NotationsTag;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\PhpdocParser
 */
class PhpdocParserTest extends TestCase
{
    /**
     * @test
     */
    public function testParseFlag()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new EmptyNotationsTag('', ['foo' => true], 'foo'),
            'bar' => new ConstantNameTag('bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * @foo\n */");
        // then
        $this->assertEquals(['foo' => true], $result);
    }

    /**
     * @test
     */
    public function testParseFlagSeveral()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new EmptyNotationsTag('', ['foo' => true], 'foo'),
            'bar' => new NotationsTag(['foo' => true], '', ['foo' => true, 'bar' => true], 'bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * @foo\n * @bar\n */");
        // then
        $this->assertEquals(['foo' => true, 'bar' => true], $result);
    }

    /**
     * @test
     */
    public function testParseValue()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new EmptyNotationsTag('hello', ['foo' => 'HELLO!'], 'foo'),
            'bar' => new ConstantNameTag('bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * @foo hello\n */");
        // then
        $this->assertSame(['foo' => 'HELLO!'], $result);
    }

    /**
     * @test
     */
    public function testParseMultiple()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new ConsecutiveProcessTag('foo', [
                new EmptyNotationsTag('', ['foo' => 1]),
                new NotationsTag(['foo' => 1], '', ['foo' => 2]),
                new NotationsTag(['foo' => 2], '', ['foo' => 3])
            ]),
            'bar' => new ConstantNameTag('bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * @foo\n * @foo\n * @foo\n */");
        // then
        $this->assertSame(['foo' => 3], $result);
    }

    /**
     * @test
     */
    public function testParseFull()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new ConsecutiveProcessTag('foo', [
                new EmptyNotationsTag('hi', ['foo' => ['hi']]),
                new NotationsTag(['foo' => ['hi'], 'bar' => true], 'bye', ['foo' => ['hi', 'bye'], 'bar' => true]),
            ]),
            'bar' => new NotationsTag(['foo' => ['hi']], '', ['foo' => ['hi'], 'bar' => true], 'bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**
 * This should be ignored, so should {@qux this}
 *
 * @foo hi
 * @bar
 * @foo bye
 * @ign
 */");
        // then
        $this->assertEquals(['foo' => ['hi', 'bye'], 'bar' => true], $result);
    }

    /**
     * @test
     */
    public function testSummary()
    {
        $doc = "/**
 * Some summery
 *
 * General description
 * spanning a few lines
 * of doc-comment.
 *
 * @bar
 * @foo bye
 * @ign
 */";

        $expected = ['summery' => 'Some summery', 'description' => "Some summery\nGeneral description\nspanning a few lines\nof doc-comment."];
        $parser = new PhpdocParser(new TagSet([
            'summery' => new EmptyNotationsTag($doc, $expected, 'summery')
        ]));
        // when
        $result = $parser->parse($doc);
        // then
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function testCallback()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new ConstantNameTag('foo'),
            'bar' => new EmptyNotationsTag('Some value', ['bar' => 'Some value'], 'bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * @bar Some value\n */", function ($argument) {
            Assert::assertSame(['bar' => 'Some value'], $argument);
            return ['value after callback'];
        });
        // then
        $this->assertSame(['value after callback'], $result);
    }

    /**
     * @test
     */
    public function testMultiline()
    {
        // given
        $expected = [
            'bar' => 'Some bar value.',
            'foo' => 'This one also has multiline value.',
            'qux' => 'Single line value'
        ];
        $parser = new PhpdocParser(new TagSet([
            'foo' => new NotationsTag(['bar' => 'Some bar value.'], 'This one also has multiline value.', ['bar' => 'Some bar value.', 'foo' => 'This one also has multiline value.'], 'foo'),
            'bar' => new EmptyNotationsTag('Some bar value.', ['bar' => 'Some bar value.'], 'bar'),
            'qux' => new NotationsTag(['bar' => 'Some bar value.', 'foo' => 'This one also has multiline value.'], 'Single line value', $expected, 'qux'),
        ]));
        // when
        $result = $parser->parse("/**
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
 */");
        // then
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function test()
    {
        // given
        $parser = new PhpdocParser(new TagSet([
            'foo' => new ConstantNameTag('foo'),
            'bar' => new ConstantNameTag('bar'),
            'qux' => new ConstantNameTag('qux'),
        ]));
        // when
        $result = $parser->parse("/**\n * Just some description. Should be ignored.\n */");
        // then
        $this->assertSame([], $result);
    }
}
