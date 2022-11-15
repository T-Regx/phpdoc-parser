<?php

namespace Test;

use Jasny\PhpdocParser\Tag;
use Jasny\PhpdocParser\TagSet;
use PHPUnit\Framework\TestCase;
use Test\Fakes\ConstantNameTag;

/**
 * @covers \Jasny\PhpdocParser\TagSet
 */
class TagSetTest extends TestCase
{
    /** @var Tag[] */
    private $tags;
    /** @var TagSet */
    private $tagSet;

    public function setUp(): void
    {
        $this->tags = [
            'foo' => new ConstantNameTag('foo'),
            'bar' => new ConstantNameTag('bar'),
            'qux' => new ConstantNameTag('qux'),
        ];

        $this->tagSet = new TagSet(array_values($this->tags));
    }

    public function testIteration()
    {
        $keys = [];

        foreach ($this->tagSet as $key => $tag) {
            $this->assertIsString($key);
            $this->assertInstanceOf(Tag::class, $tag);

            $this->assertArrayHasKey($key, $this->tagSet);
            $this->assertSame($this->tagSet[$key], $tag);

            $keys[] = $key;
        }

        $this->assertSame(['foo', 'bar', 'qux'], $keys);
    }

    public function testWithTagSet()
    {
        $newTags = [
            'red' => new ConstantNameTag('red'),
            'blue' => new ConstantNameTag('blue'),
            'green' => new ConstantNameTag('green')
        ];
        $new = new TagSet(array_values($newTags));

        $combined = $this->tagSet->with($new);

        $this->assertInstanceOf(TagSet::class, $combined);
        $this->assertEquals(['foo', 'bar', 'qux', 'red', 'blue', 'green'], array_keys(iterator_to_array($combined)));
        $this->assertSame($this->tags + $newTags, iterator_to_array($combined));

        $this->assertNotSame($this->tagSet, $combined);
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagSet)));

        $this->assertNotSame($new, $combined);
        $this->assertEquals(['red', 'blue', 'green'], array_keys(iterator_to_array($new)));
    }

    public function testWithArray()
    {
        $newTags = [
            'red' => new ConstantNameTag('red'),
            'blue' => new ConstantNameTag('blue'),
            'green' => new ConstantNameTag('green')
        ];
        $new = array_values($newTags);

        $combined = $this->tagSet->with($new);

        $this->assertEquals(['foo', 'bar', 'qux', 'red', 'blue', 'green'], array_keys(iterator_to_array($combined)));
        $this->assertSame($this->tags + $newTags, iterator_to_array($combined));

        $this->assertNotSame($this->tagSet, $combined);
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagSet)));
    }

    public function testWithout()
    {
        $filtered = $this->tagSet->without('bar');

        $this->assertInstanceOf(TagSet::class, $filtered);
        $this->assertNotSame($this->tagSet, $filtered);

        $this->assertEquals(['foo', 'qux'], array_keys(iterator_to_array($filtered)));
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagSet)));
    }

    public function testWithoutMultiple()
    {
        $filtered = $this->tagSet->without('foo', 'bar');

        $this->assertInstanceOf(TagSet::class, $filtered);
        $this->assertNotSame($this->tagSet, $filtered);

        $this->assertEquals(['qux'], array_keys(iterator_to_array($filtered)));
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagSet)));
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->tagSet['foo']));
        $this->assertFalse(isset($this->tagSet['non-existent']));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->tags['foo'], $this->tagSet['foo']);
        $this->assertSame($this->tags['bar'], $this->tagSet['bar']);
    }

    public function testOffsetGetNonExistent()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage("Unknown tag '@non-existent'; Use isset to see if tag is defined");

        $this->tagSet['non-existent'];
    }

    public function testOffsetSet()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->tagSet['shape'] = 'round';
    }

    public function testOffsetUnset()
    {
        $this->expectException(\BadMethodCallException::class);

        unset($this->tagSet['foo']);
    }
}
