<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

class CustomTag extends AbstractTag
{
    /** @var callable */
    protected $process;

    public function __construct(string $name, callable $process)
    {
        parent::__construct($name);

        $this->process = $process;
    }

    public function process(array $notations, string $value): array
    {
        return call_user_func($this->process, $notations, $value);
    }
}
