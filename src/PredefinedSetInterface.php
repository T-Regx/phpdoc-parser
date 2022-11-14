<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser;

interface PredefinedSetInterface
{
    public static function tags(): TagSet;
}
