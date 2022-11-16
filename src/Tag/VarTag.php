<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;

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
    /** @var Pattern */
    private $variablePattern;

    public function __construct(string $tagName, array $additional, ClassName $className)
    {
        $this->name = $tagName;
        $this->additional = $additional;
        $this->className = $className;
        $this->variablePattern = Pattern::of('^(?:(?<type>[^$\s]+)\s*)?(?:\$(?<name>\w+)\s*)?(?:"(?<id>[^"]+)"\s*)?(?:(?<description>.+))?', 's');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $variableTag = $this->variablePattern->match($value)->first();

        $variable = [];

        if ($variableTag->matched('name')) {
            $variable['name'] = $variableTag->get('name');
        }
        if ($variableTag->matched('type') && $variableTag->get('type') !== '') {
            $variable['type'] = $this->className->apply($variableTag->get('type'));
        }
        if ($variableTag->matched('id')) {
            $variable['id'] = $variableTag->get('id');
        }
        if ($variableTag->matched('description')) {
            $variable['description'] = trim($variableTag->get('description'));
        }

        $notations[$this->name] = $variable + $this->additional;
        return $notations;
    }
}
