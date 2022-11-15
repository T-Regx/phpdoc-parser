<?php
namespace Jasny\PhpdocParser\Tag\PhpDocumentor;

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
    protected $additional;
    /** @var callable|null */
    protected $fqsenConvertor;

    public function __construct(string $tagName, ?callable $fqsenConvertor = null, array $additional = [])
    {
        $this->name = $tagName;
        $this->fqsenConvertor = $fqsenConvertor;
        $this->additional = $additional;
    }

    public function getAdditionalProperties(): array
    {
        return $this->additional;
    }

    public function process(array $notations, string $value): array
    {
        $regexp = '/^(?:(?<type>[^$\s]+)\s*)?(?:\$(?<name>\w+)\s*)?(?:"(?<id>[^"]+)"\s*)?(?:(?<description>.+))?/';
        preg_match($regexp, $value, $props); //regexp won't fail

        $this->removeEmptyValues($props);

        if (isset($props['type']) && isset($this->fqsenConvertor)) {
            $props['type'] = call_user_func($this->fqsenConvertor, $props['type']);
        }

        $props = array_only($props, ['type', 'name', 'id', 'description']);

        $notations[$this->name] = $props + $this->additional;

        return $notations;
    }

    protected function removeEmptyValues(array &$props): void
    {
        foreach (['type', 'name', 'id'] as $name) {
            if (isset($props[$name]) && $props[$name] === '') {
                unset($props[$name]);
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
