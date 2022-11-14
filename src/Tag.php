<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser;

interface Tag
{
    public function getName(): string;

    public function process(array $notations, string $value): array;
}