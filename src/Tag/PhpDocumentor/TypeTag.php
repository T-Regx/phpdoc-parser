<?php
namespace Jasny\PhpdocParser\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;

/**
 * Use the first word of tag as type, the rest as desciption
 */
class TypeTag implements Tag
{
    /** @var string */
    private $name;
    /** @var callable|null */
    protected $fqsenConvertor;

    /**
     * @param string $name
     * @param callable|null $fqsenConvertor Logic to convert class to FQCN
     */
    public function __construct(string $name, ?callable $fqsenConvertor = null)
    {
        $this->name = $name;

        $this->fqsenConvertor = $fqsenConvertor;
    }

    public function process(array $notations, string $value): array
    {
        if ($value === '') {
            throw new PhpdocException("Failed to parse '@{$this->name}': tag value should not be empty");
        }

        preg_match('/^(?<type>\S+)(?:\s+(?<description>.*))?/', $value, $data); //regexp won't fail

        $this->processType($data);
        $data = array_only($data, ['type', 'description']);

        $notations[$this->name] = $data;

        return $notations;
    }

    protected function processType(array &$data): void
    {
        if (isset($data['type']) && $this->fqsenConvertor !== null) {
            $data['type'] = call_user_func($this->fqsenConvertor, $data['type']);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
