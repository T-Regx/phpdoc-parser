<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;

/**
 * Custom logic for PhpDocumentor 'var', 'param' and 'property' tag
 */
class VarTag implements Tag
{
    /** @var string */
    private $name;
    /** @var array */
    private $additional;
    /** @var ClassName */
    private $className;

    public function __construct(string $tagName, array $additional, ClassName $className)
    {
        $this->name = $tagName;
        $this->additional = $additional;
        $this->className = $className;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $regexp = '/^(?:(?<type>[^$\s]+)\s*)?(?:\$(?<name>\w+)\s*)?(?:"(?<id>[^"]+)"\s*)?(?:(?<description>.+))?/s';
        preg_match($regexp, $value, $props); //regexp won't fail
        foreach (['type', 'name', 'id'] as $name) {
            if (isset($props[$name]) && $props[$name] === '') {
                unset($props[$name]);
            }
        }
        if (isset($props['type'])) {
            $props['type'] = $this->className->apply($props['type']);
        }

        if (\array_key_exists('description', $props)) {
            $props['description'] = trim($props['description']);
        }

        $props = array_only($props, ['type', 'name', 'id', 'description']);
        $notations[$this->name] = $props + $this->additional;
        return $notations;
    }
}
