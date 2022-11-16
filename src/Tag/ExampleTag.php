<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;

class ExampleTag implements Tag
{
    /** @var string */
    private $name;
    private $examplePattern;

    public function __construct(string $tagName)
    {
        $this->name = $tagName;
        $this->examplePattern = Pattern::of('^(?<location>(?:[^"]\S*|"[^"]+"))(?:\s*(?<start_line>\d+)(?:\s*(?<number_of_lines>\d+))?)?(?:\s*(?<description>.+))?');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $matcher = $this->examplePattern->match($value);
        if ($matcher->fails()) {
            throw new PhpdocException("Failed to parse '@{$this->name} $value': invalid syntax");
        }
        $match = $matcher->first();

        $matches = [];
        $matches['location'] = \trim($match->get('location'), '"');

        foreach (['start_line', 'number_of_lines'] as $name) {
            if ($match->matched($name)) {
                if ($match->get($name) !== '') {
                    $matches[$name] = $match->group($name)->toInt();
                }
            }
        }
        if ($match->matched('description')) {
            $matches['description'] = $match->get('description');
        }
        $notations[$this->name] = $matches;
        return $notations;
    }
}
