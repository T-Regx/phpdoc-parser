<?php
namespace Test\Fixtures;

use PHPUnit\Framework\Assert;

class Functions
{
    public static function fail(string $message = null): callable
    {
        return function () use ($message): void {
            Assert::fail($message);
        };
    }

    public static function prepend(string $prefix): callable
    {
        return function (string $value) use ($prefix): string {
            return $prefix . $value;
        };
    }
}
