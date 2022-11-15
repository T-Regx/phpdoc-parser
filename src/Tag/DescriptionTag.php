<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class DescriptionTag implements Tag
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
        $notations[$this->name] = $value;
        return $notations;
    }
}
