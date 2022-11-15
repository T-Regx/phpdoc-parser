<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\Tag\CustomTag;
use Jasny\PhpdocParser\Tests\Fixtures\Functions;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\CustomTag
 */
class CustomTagTest extends TestCase
{
    public function testGetName()
    {
        // given
        $tag = new CustomTag('foo', Functions::fail());
        // when, then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testProcess()
    {
        // given
        $tag = new CustomTag('foo', function (...$args) {
            Assert::assertSame([['bar' => 1], 'foo-42'], $args);
            return ['foo' => 42];
        });
        // when
        $result = $tag->process(['bar' => 1], 'foo-42');
        // then
        $this->assertEquals(['foo' => 42], $result);
    }
}
