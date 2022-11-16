<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;
use function trim;

class ExampleTag implements Tag
{
    /** @var string */
    private $name;

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
        $regexp = '/^(?<location>(?:[^"]\S*|"[^"]+"))(?:\s*(?<start_line>\d+)(?:\s*(?<number_of_lines>\d+))?)?(?:\s*(?<description>.+))?/';
        if (!preg_match($regexp, $value, $matches)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $value': invalid syntax");
        }
        $matches['location'] = trim($matches['location'], '"');
        foreach (['start_line', 'number_of_lines'] as $name) {
            if (!isset($matches[$name])) {
                continue;
            }
            if ($matches[$name] === '') {
                unset($matches[$name]);
            } else {
                $matches[$name] = (int)$matches[$name];
            }
        }
        $matches = array_only($matches, ['location', 'start_line', 'number_of_lines', 'description']);
        $notations[$this->name] = $matches;
        return $notations;
    }
}
