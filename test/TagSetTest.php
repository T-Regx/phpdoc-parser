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

            $this->assertTrue($this->tagSet->tagExists($key));
            $this->assertSame($this->tagSet->tagByName($key), $tag);

            $keys[] = $key;
        }

        $this->assertSame(['foo', 'bar', 'qux'], $keys);
    }

    public function testWithTagSet()
    {
        $newTags = [
            'red'   => new ConstantNameTag('red'),
            'blue'  => new ConstantNameTag('blue'),
            'green' => new ConstantNameTag('green')
        ];
        $new = new TagSet(array_values($newTags));

        $combined = $this->tagSet->with($new);

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
            'red'   => new ConstantNameTag('red'),
            'blue'  => new ConstantNameTag('blue'),
            'green' => new ConstantNameTag('green')
        ];
        $new = array_values($newTags);

        $combined = $this->tagSet->with($new);

        $this->assertEquals(['foo', 'bar', 'qux', 'red', 'blue', 'green'], array_keys(iterator_to_array($combined)));
        $this->assertSame($this->tags + $newTags, iterator_to_array($combined));

        $this->assertNotSame($this->tagSet, $combined);
        $this->assertEquals(['foo', 'bar', 'qux'], array_keys(iterator_to_array($this->tagSet)));
    }
}
