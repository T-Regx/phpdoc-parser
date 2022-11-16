<?php
namespace Jasny\PhpdocParser\Tag;

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
    /** @var callable|null */
    private $fqsenConvertor;

    public function __construct(string $tagName, ?callable $fqsenConvertor, array $additional)
    {
        $this->name = $tagName;
        $this->fqsenConvertor = $fqsenConvertor;
        $this->additional = $additional;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $regexp = '/^(?:(?<type>[^$\s]+)\s*)?(?:\$(?<name>\w+)\s*)?(?:"(?<id>[^"]+)"\s*)?(?:(?<description>.+))?/';
        preg_match($regexp, $value, $props); //regexp won't fail
        foreach (['type', 'name', 'id'] as $name) {
            if (isset($props[$name]) && $props[$name] === '') {
                unset($props[$name]);
            }
        }
        if (isset($props['type']) && isset($this->fqsenConvertor)) {
            $props['type'] = call_user_func($this->fqsenConvertor, $props['type']);
        }
        $props = array_only($props, ['type', 'name', 'id', 'description']);
        $notations[$this->name] = $props + $this->additional;
        return $notations;
    }
}
