<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\TagInterface;

class ModifyTag implements TagInterface, ProxyTagInterface
{
    /** @var TagInterface */
    protected $tag;
    /** @var callable */
    protected $logic;

    public function __construct(TagInterface $tag, callable $logic)
    {
        $this->tag = $tag;
        $this->logic = $logic;
    }

    public function getName(): string
    {
        return $this->tag->getName();
    }

    public function getTag(): TagInterface
    {
        return $this->tag;
    }

    public function process(array $notations, string $value): array
    {
        return call_user_func($this->logic, $notations, $this->tag->process([], $value), $value);
    }
}
