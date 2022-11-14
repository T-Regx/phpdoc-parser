<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

class FlagTag extends AbstractTag
{
    public function process(array $notations, string $value): array
    {
        $notations[$this->name] = true;
        return $notations;
    }
}
