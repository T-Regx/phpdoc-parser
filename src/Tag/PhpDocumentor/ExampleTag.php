<?php
namespace Jasny\PhpdocParser\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;

class ExampleTag implements Tag
{
    /** @var string */
    protected $name;

    public function __construct(string $tagName)
    {
        $this->name = $tagName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $regexp = '/^(?<location>(?:[^"]\S*|"[^"]+"))(?:\s*(?<start_line>\d+)'
            . '(?:\s*(?<number_of_lines>\d+))?)?(?:\s*(?<description>.+))?/';

        if (!preg_match($regexp, $value, $matches)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $value': invalid syntax");
        }

        $this->normalizeValues($matches);
        $matches = array_only($matches, ['location', 'start_line', 'number_of_lines', 'description']);

        $notations[$this->name] = $matches;

        return $notations;
    }

    protected function normalizeValues(array &$values): void
    {
        $values['location'] = trim($values['location'], '"');

        foreach (['start_line', 'number_of_lines'] as $name) {
            if (!isset($values[$name])) {
                continue;
            }

            if ($values[$name] === '') {
                unset($values[$name]);
            } else {
                $values[$name] = (int)$values[$name];
            }
        }
    }
}
