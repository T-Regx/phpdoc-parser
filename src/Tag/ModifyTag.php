<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class ModifyTag implements Tag, ProxyTagInterface
{
    /** @var Tag */
    protected $tag;
    /** @var callable */
    protected $logic;

    public function __construct(Tag $tag, callable $logic)
    {
        $this->tag = $tag;
        $this->logic = $logic;
    }

    public function getName(): string
    {
        return $this->tag->getName();
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function process(array $notations, string $value): array
    {
        return call_user_func($this->logic, $notations, $this->tag->process([], $value), $value);
    }
}
