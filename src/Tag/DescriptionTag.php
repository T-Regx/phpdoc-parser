<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

class DescriptionTag extends AbstractTag
{
    public function process(array $notations, string $value): array
    {
        $notations[$this->name] = $value;
        return $notations;
    }
}
