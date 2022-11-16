<?php
namespace Jasny\PhpdocParser\ClassName;

class FunctionClassName extends ClassName
{
    /** @var callable */
    private $fullyQualifiedName;

    public function __construct(callable $fullyQualifiedName)
    {
        $this->fullyQualifiedName = $fullyQualifiedName;
    }

    public function apply(string $className): string
    {
        $return = ($this->fullyQualifiedName)($className);
        if (\is_string($return)) {
            return $return;
        }
        throw new \RuntimeException("Expected the fully qualified name to be string");
    }
}
