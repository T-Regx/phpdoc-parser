<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

interface ProxyTagInterface
{
    public function getTag(): Tag;
}
