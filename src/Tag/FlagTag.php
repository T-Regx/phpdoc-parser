<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class FlagTag implements Tag
{

    /** @var string */
    protected $name;

    public function __construct(string $tagName)
    {
        $this->name = $tagName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $notations[$this->name] = true;
        return $notations;
    }
}
