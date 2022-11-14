<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;

class RegExpTag extends AbstractTag
{
    /** @var string */
    protected $regexp;

    public function __construct(string $name, string $regexp)
    {
        parent::__construct($name);

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
}
