<?php
namespace Jasny\PhpdocParser;

class TagSet implements \IteratorAggregate
{
    /** @var Tag[] */
    private $tags = [];

    /**
     * @param iterable|Tag[] $tags
     */
    public function __construct(iterable $tags)
    {
        foreach ($tags as $tag) {
            $this->tags[$tag->getName()] = $tag;
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tags);
    }

    public function with(iterable $tags): TagSet
    {
        if ($tags instanceof \Traversable) {
            $tagArray = iterator_to_array($tags, false);
        } else {
            $tagArray = $tags;
        }

        return new TagSet(array_merge(array_values($this->tags), $tagArray));
    }

    public function tagByName(string $tagName): Tag
    {
        return $this->tags[$tagName];
    }

    public function tagExists(string $tagName): bool
    {
        return \array_key_exists($tagName, $this->tags);
    }
}
