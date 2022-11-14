<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\TagInterface;

interface ProxyTagInterface
{
    public function getTag(): TagInterface;
}
