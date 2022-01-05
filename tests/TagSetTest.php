<?php

namespace Jasny\PhpdocParser\Tests;

use Jasny\PhpdocParser\TagInterface;
use Jasny\PhpdocParser\TagSet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\TagSet
 */
class TagSetTest extends TestCase
{
    /**
     * @var TagInterface[]|MockObject[]
     */
    protected $tags;

    /**
     * @var TagSet
     */
    protected $tagset;

    public function setUp(): void
    {
        $this->tags = [
            'foo' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'foo']),
            'bar' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'bar']),
            'qux' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'qux']),
        ];

        $this->tagset = new TagSet(array_values($this->tags));
    }

    public function testIteration()
    {
        $keys = [];

        foreach ($this->tagset as $key => $tag) {
            $this->assertIsString($key);
            $this->assertInstanceOf(TagInterface::class, $tag);

            $this->assertArrayHasKey($key, $this->tagset);
            $this->assertSame($this->tagset[$key], $tag);

            $keys[] = $key;
        }

        $this->assertSame(['foo', 'bar', 'qux'], $keys);
    }

    public function testWithTagSet()
    {
        $newTags = [
            'red' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'red']),
            'blue' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'blue']),
            'green' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'green'])
        ];
        $new = new TagSet(array_values($newTags));

        $combined = $this->tagset->with($new);

        $this->assertInstanceOf(TagSet::class, $combined);
        $this->assertEquals(['foo', 'bar', 'qux', 'red', 'blue', 'green'], array_keys(iterator_to_array($combined)));
        $this->assertSame($this->tags + $newTags, iterator_to_array($combined));

        $this->assertNotSame($this->tagset, $combined);
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagset)));

        $this->assertNotSame($new, $combined);
        $this->assertEquals(['red', 'blue', 'green'], array_keys(iterator_to_array($new)));
    }

    public function testWithArray()
    {
        $newTags = [
            'red' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'red']),
            'blue' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'blue']),
            'green' => $this->createConfiguredMock(TagInterface::class, ['getName' => 'green'])
        ];
        $new = array_values($newTags);

        $combined = $this->tagset->with($new);

        $this->assertInstanceOf(TagSet::class, $combined);
        $this->assertEquals(['foo', 'bar', 'qux', 'red', 'blue', 'green'], array_keys(iterator_to_array($combined)));
        $this->assertSame($this->tags + $newTags, iterator_to_array($combined));

        $this->assertNotSame($this->tagset, $combined);
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagset)));
    }

    public function testWithout()
    {
        $filtered = $this->tagset->without('bar');

        $this->assertInstanceOf(TagSet::class, $filtered);
        $this->assertNotSame($this->tagset, $filtered);

        $this->assertEquals(['foo', 'qux'], array_keys(iterator_to_array($filtered)));
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagset)));
    }

    public function testWithoutMultiple()
    {
        $filtered = $this->tagset->without('foo', 'bar');

        $this->assertInstanceOf(TagSet::class, $filtered);
        $this->assertNotSame($this->tagset, $filtered);

        $this->assertEquals(['qux'], array_keys(iterator_to_array($filtered)));
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagset)));
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->tagset['foo']));
        $this->assertFalse(isset($this->tagset['non-existent']));
    }

    public function testOffsetGet()
    {
        $this->assertSame($this->tags['foo'], $this->tagset['foo']);
        $this->assertSame($this->tags['bar'], $this->tagset['bar']);
    }

    public function testOffsetGetNonExistent()
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage("Unknown tag '@non-existent'; Use isset to see if tag is defined");
        
        $this->tagset['non-existent'];
    }

    public function testOffsetSet()
    {
        $this->expectException(\BadMethodCallException::class);
        
        $this->tagset['shape'] = 'round';
    }

    public function testOffsetUnset()
    {
        $this->expectException(\BadMethodCallException::class);
    
        unset($this->tagset['foo']);
    }
}
