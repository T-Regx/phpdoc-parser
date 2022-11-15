<?php

namespace Test\Tag;

use Jasny\PhpdocParser\Tag\DescriptionTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\DescriptionTag
 */
class DescriptionTagTest extends TestCase
{
    public function testGetName()
    {
        // given
        $tag = new DescriptionTag('foo');
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testProcess()
    {
        // given
        $tag = new DescriptionTag('foo');
        // when
        $result = $tag->process(['bar' => 1], 'Hello this is "the text"');
        // then
        $this->assertEquals(['bar' => 1, 'foo' => 'Hello this is "the text"'], $result);
    }
}
