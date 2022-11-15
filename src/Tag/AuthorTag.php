<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;

class AuthorTag implements Tag
{
    /** @var string */
    private $name;
    /** @var string */
    private $regexp;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->regexp = '/^(?:(?<name>(?:[^\<]\S*\s+)*[^\<]\S*)?\s*)?(?:\<(?<email>[^\>]+)\>)?/';
    }

    public function process(array $notations, string $value): array
    {
        if (!preg_match($this->regexp, $value, $matches)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $value': invalid syntax");
        }
        $notations[$this->name] = $matches;
        return $notations;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
