<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;

class TypeTag implements Tag
{
    /** @var string */
    private $name;
    /** @var ClassName */
    private $className;
    /** @var Pattern */
    private $typePattern;

    public function __construct(string $name, ClassName $className)
    {
        $this->name = $name;
        $this->className = $className;
        $this->typePattern = Pattern::of('^(?<type>\S+)(?:\s+(?<description>.*))?');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        if ($value === '') {
            throw new PhpdocException("Failed to parse '@{$this->name}': tag value should not be empty");
        }
        $match = $this->typePattern->match($value)->first();
        $data = [];
        if ($match->matched('type')) {
            $data['type'] = $this->className->apply($match->get('type'));
        }
        if ($match->matched('description')) {
            $data['description'] = $match->get('description');
        }
        $notations[$this->name] = $data;
        return $notations;
    }
}
