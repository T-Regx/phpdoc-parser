<?php
namespace Test\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\MultiTag;
use PHPUnit\Framework\TestCase;
use Test\Fakes\EmptyNotationsTag;

/**
 * @covers \Jasny\PhpdocParser\Tag\MultiTag
 */
class MultiTagTest extends TestCase
{
    public function testProcess()
    {
        // given
        $tag = new MultiTag('foos', new EmptyNotationsTag('three', ['foo' => '3'], 'name'));
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
        $tag = new MultiTag('foos', new EmptyNotationsTag('goodbye', ['foo' => ['name' => 'two', 'desc' => 'bye']], 'name'), 'name');
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
        $tag = new MultiTag('foos', new EmptyNotationsTag('goodbye', ['foo' => ['desc' => 'bye']], 'foo'), 'name');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Unable to add '@foo goodbye' tag: No name");
        // when
        $tag->process(['foos' => ['one' => ['name' => 'one', 'desc' => 'hi']]], 'goodbye');
    }

    public function testProcessKeyDuplicate()
    {
        // given
        $tag = new MultiTag('foos', new EmptyNotationsTag('goodbye', ['foo' => ['name' => 'one', 'desc' => 'bye']], 'foo'), 'name');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Unable to add '@foo goodbye' tag: Duplicate name 'one'");
        // when
        $tag->process(['foos' => ['one' => ['name' => 'one', 'desc' => 'hi']]], 'goodbye');
    }
}
