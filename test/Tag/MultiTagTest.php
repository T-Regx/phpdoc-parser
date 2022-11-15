<?php

namespace Test\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\MultiTag;
use PHPUnit\Framework\TestCase;
use Test\Fakes\ConstantNameTag;
use Test\Fakes\EmptyNotationsTag;

/**
 * @covers \Jasny\PhpdocParser\Tag\MultiTag
 */
class MultiTagTest extends TestCase
{
    public function testGetName()
    {
        // given
        $tag = new MultiTag('foos', new ConstantNameTag('foo'));
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testGetKey()
    {
        // given
        $tag = new MultiTag('foos', new ConstantNameTag('foo'));
        // when, then
        $this->assertEquals('foos', $tag->getKey());
    }

    public function testProcess()
    {
        // given
        $processedTag = new EmptyNotationsTag('three', ['foo' => '3'], 'name');
        $tag = new MultiTag('foos', $processedTag);
        // when
        $result = $tag->process(['foos' => ['one', 'two']], 'three');
        // then
        $this->assertEquals(['foos' => ['one', 'two', '3']], $result);
    }

    public function testProcessLogicException()
    {
        // given
        $tag = new MultiTag('foos', new EmptyNotationsTag('three', ['foo' => '3', 'bar' => '2'], 'foo'));
        // then
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Unable to parse '@foo' tag: Multi tags must result in exactly one notation per tag.");
        // when
        $tag->process(['foos' => ['one', 'two']], 'three');
    }

    public function testProcessKey()
    {
        // given
        $processedTag = new EmptyNotationsTag('goodbye', ['foo' => ['name' => 'two', 'desc' => 'bye']], 'name');
        $tag = new MultiTag('foos', $processedTag, 'name');
        // when
        $result = $tag->process(['foos' => ['one' => ['name' => 'one', 'desc' => 'hi']]], 'goodbye');
        // then
        $expected = [
            'foos' => [
                'one' => ['name' => 'one', 'desc' => 'hi'],
                'two' => ['name' => 'two', 'desc' => 'bye']
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testProcessKeyUnknown()
    {
        // given
        $processedTag = new EmptyNotationsTag('goodbye', ['foo' => ['desc' => 'bye']], 'foo');
        $tag = new MultiTag('foos', $processedTag, 'name');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Unable to add '@foo goodbye' tag: No name");
        // when
        $tag->process(['foos' => ['one' => ['name' => 'one', 'desc' => 'hi']]], 'goodbye');
    }

    public function testProcessKeyDuplicate()
    {
        // given
        $processedTag = new EmptyNotationsTag('goodbye', ['foo' => ['name' => 'one', 'desc' => 'bye']], 'foo');
        $tag = new MultiTag('foos', $processedTag, 'name');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Unable to add '@foo goodbye' tag: Duplicate name 'one'");
        // when
        $tag->process(['foos' => ['one' => ['name' => 'one', 'desc' => 'hi']]], 'goodbye');
    }
}
