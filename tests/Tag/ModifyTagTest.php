<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\Tag\ModifyTag;
use Jasny\PhpdocParser\TagInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\ModifyTag
 */
class ModifyTagTest extends TestCase
{
    public function testGetName()
    {
        /** @var MockObject|TagInterface $mockTag */
        $mockTag = $this->createConfiguredMock(TagInterface::class, ['getName' => 'foo']);

        $tag = new ModifyTag($mockTag, function () {
        });

        $this->assertEquals('foo', $tag->getName());
    }

    public function testGetTag()
    {
        /** @var MockObject|TagInterface $mockTag */
        $mockTag = $this->createMock(TagInterface::class);

        $tag = new ModifyTag($mockTag, function () {
        });

        $this->assertSame($mockTag, $tag->getTag());
    }

    public function testProcess()
    {
        /** @var MockObject|TagInterface $mockTag */
        $mockTag = $this->createMock(TagInterface::class);
        $mockTag->expects($this->once())->method('process')->with([], 'one two')
            ->willReturn(['foo' => 42]);

        $tag = new ModifyTag($mockTag, function (...$actual) {
            Assert::assertSame([['bar' => 1], ['foo' => 42], 'one two'], $actual);
            return ['bar' => 2, 'foo' => 3];
        });

        $result = $tag->process(['bar' => 1], 'one two');

        $this->assertEquals(['bar' => 2, 'foo' => 3], $result);
    }
}
