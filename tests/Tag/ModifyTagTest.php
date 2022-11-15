<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\Tag\ModifyTag;
use Jasny\PhpdocParser\Tests\Fakes\ConstantNameTag;
use Jasny\PhpdocParser\Tests\Fakes\EmptyNotationsTag;
use Jasny\PhpdocParser\Tests\Fixtures\Functions;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\ModifyTag
 */
class ModifyTagTest extends TestCase
{
    public function testGetName()
    {
        // when
        $tag = new ModifyTag(new ConstantNameTag('foo'), Functions::fail());
        // then
        $this->assertEquals('foo', $tag->getName());
    }

    public function testProcess()
    {
        // given
        $tag = new ModifyTag(new EmptyNotationsTag('one two', ['foo' => 42]), function (...$actual) {
            Assert::assertSame([['bar' => 1], ['foo' => 42], 'one two'], $actual);
            return ['bar' => 2, 'foo' => 3];
        });
        // when
        $result = $tag->process(['bar' => 1], 'one two');
        // then
        $this->assertEquals(['bar' => 2, 'foo' => 3], $result);
    }
}
