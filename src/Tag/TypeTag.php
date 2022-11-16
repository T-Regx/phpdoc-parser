<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;

class TypeTag implements Tag
{
    /** @var string */
    private $name;
    /** @var ClassName */
    private $className;

    public function __construct(string $name, ClassName $className)
    {
        $this->name = $name;
        $this->className = $className;
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
        preg_match('/^(?<type>\S+)(?:\s+(?<description>.*))?/', $value, $data); //regexp won't fail
        if (isset($data['type'])) {
            $data['type'] = $this->className->apply($data['type']);
        }
        $data = array_only($data, ['type', 'description']);
        $notations[$this->name] = $data;
        return $notations;
    }
}
