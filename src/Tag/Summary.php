<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class Summary implements Tag
{
    public function process(array $notations, string $value): array
    {
        preg_match_all('/^\s*(?:(?:\/\*)?\*\s*)?([^@\s\/*].*?)\r?$/m', $value, $matches, PREG_PATTERN_ORDER);

        if (!isset($matches[1]) || $matches[1] === []) {
            return $notations;
        }

        $matches = $matches[1];

        $notations['summery'] = reset($matches);
        $notations['description'] = implode("\n", $matches);

        return $notations;
    }

    public function getName(): string
    {
        return 'summery';
    }
}
