<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;

class RegExpTag implements Tag
{
    /** @var string */
    private $name;
    /** @var string */
    protected $regexp;

    public function __construct(string $name, string $regexp)
    {
        $this->name = $name;
        $this->regexp = $regexp;
    }

    public function getRegExp(): string
    {
        return $this->regexp;
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
