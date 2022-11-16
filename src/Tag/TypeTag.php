<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function call_user_func;
use function Jasny\array_only;

class TypeTag implements Tag
{
    /** @var string */
    private $name;
    /** @var callable|null */
    private $fqsenConvertor;

    public function __construct(string $name, ?callable $fqsenConvertor)
    {
        $this->name = $name;
        $this->fqsenConvertor = $fqsenConvertor;
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
        if (isset($data['type']) && $this->fqsenConvertor !== null) {
            $data['type'] = call_user_func($this->fqsenConvertor, $data['type']);
        }
        $data = array_only($data, ['type', 'description']);
        $notations[$this->name] = $data;
        return $notations;
    }
}
