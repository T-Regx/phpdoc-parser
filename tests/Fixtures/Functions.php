<?php
namespace Jasny\PhpdocParser\Tests\Fixtures;

use PHPUnit\Framework\Assert;

class Functions
{
    public static function fail(string $message = null): callable
    {
        return function () use ($message): void {
            Assert::fail($message);
        };
    }
}
