<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class MultiTag implements Tag
{
    /** @var string */
    private $key;
    /** @var Tag */
    private $tag;

    public function __construct(string $notationKey, Tag $tag)
    {
        $this->key = $notationKey;
        $this->tag = $tag;
    }

    public function getName(): string
    {
        return $this->tag->getName();
    }

    public function process(array $notations, string $value): array
    {
        $tagNotations = $this->tag->process([], $value);
        if (count($tagNotations) === 1) {
            $notations[$this->key][] = \reset($tagNotations);
            return $notations;
        }
        throw new \LogicException("Unable to parse '@{$this->tag->getName()}' tag: Multi tags must result in exactly one notation per tag.");
    }
}
