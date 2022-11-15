<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\Tag\FlagTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\FlagTag
 */
class FlagTagTest extends TestCase
{
    public function testGetName()
    {
        // given
        $tag = new FlagTag('foo');
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testProcess()
    {
        // given
        $tag = new FlagTag('foo');
        // when
        $result = $tag->process(['bar' => 42], '');
        // then
        $this->assertEquals(['bar' => 42, 'foo' => true], $result);
    }

    public function testProcessDescription()
    {
        // given
        $tag = new FlagTag('foo');
        // when
        $result = $tag->process([], 'this is ignored');
        // then
        $this->assertEquals(['foo' => true], $result);
    }
}
