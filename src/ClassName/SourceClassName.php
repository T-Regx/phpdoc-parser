<?php
namespace Jasny\PhpdocParser\ClassName;

class SourceClassName extends ClassName
{
    public function apply(string $className): string
    {
        return $className;
    }
}
