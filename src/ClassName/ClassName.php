<?php
namespace Jasny\PhpdocParser\ClassName;

abstract class ClassName
{
    public abstract function apply(string $className): string;

    public static function ofNullable(?callable $fullyQualifiedName): ClassName
    {
        if ($fullyQualifiedName === null) {
            return new SourceClassName();
        }
        return new FunctionClassName($fullyQualifiedName);
    }
}
