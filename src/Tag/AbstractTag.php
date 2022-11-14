<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

abstract class AbstractTag implements Tag
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
}
