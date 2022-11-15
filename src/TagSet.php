<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser;

use function Jasny\array_without;
use function Jasny\expect_type;

class TagSet implements \IteratorAggregate, \ArrayAccess
{
    /** @var Tag[] */
    protected $tags = [];

    /**
     * @param iterable|Tag[] $tags
     */
    public function __construct(iterable $tags)
    {
        foreach ($tags as $tag) {
            expect_type($tag, Tag::class);
            $this->tags[$tag->getName()] = $tag;
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tags);
    }

    public function with(iterable $tags): TagSet
    {
        $tagArray = $tags instanceof \Traversable ? iterator_to_array($tags, false) : $tags;

        return new static(array_merge(array_values($this->tags), $tagArray));
    }

    public function without(string ...$tags): self
    {
        return new static(array_without($this->tags, $tags));
    }

    public function offsetExists($key): bool
    {
        return isset($this->tags[$key]);
    }

    public function offsetGet($key): Tag
    {
        if (!isset($this->tags[$key])) {
            throw new \OutOfBoundsException("Unknown tag '@{$key}'; Use isset to see if tag is defined.");
        }

        return $this->tags[$key];
    }

    public function offsetSet($key, $tag): void
    {
        throw new \BadMethodCallException("A tagset is immutable; Use `with()` instead.");
    }

    public function offsetUnset($key): void
    {
        throw new \BadMethodCallException("A tagset is immutable; Use `without()` instead.");
    }
}
