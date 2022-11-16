<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class SummaryAndDescription implements Tag
{
    public function getName(): string
    {
        return 'summary';
    }

    public function process(array $notations, string $value): array
    {
        \preg_match_all('/^\s*(?:(?:\/\*)?\*\s*)?([^@\s\/*].*?)?$/m', $value, $lines, PREG_PATTERN_ORDER);

        if (!isset($lines[1]) || $lines[1] === []) {
            return $notations;
        }
        $lines = $lines[1];
        [$summary, $newDescription] = $this->parsedSummaryAndDescription(\implode("\n", $lines));
        if ($summary !== '') {
            $notations['summary'] = trim(\preg_replace('/\s+/', ' ', $summary));
        }
        if ($newDescription !== '') {
            $notations['description'] = \trim($newDescription);
        }

        return $notations;
    }

    private function parsedSummaryAndDescription(string $implode): array
    {
        $splitElements = \preg_split('/(\.|\n\n)/', \trim($implode), 2, PREG_SPLIT_DELIM_CAPTURE);
        if (\count($splitElements) === 3) {
            [$summary, $delimiter, $description] = $splitElements;
            return [$summary . $delimiter, $description];
        }
        return [$splitElements[0], ''];
    }
}
