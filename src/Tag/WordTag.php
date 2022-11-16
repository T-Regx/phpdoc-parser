<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;
use function Jasny\str_before;

/**
 * Only use the first word after the tag, ignoring the rest
 */
class WordTag implements Tag
{
    /** @var string */
    private $name;
    /** @var string|bool */
    private $default;

    public function __construct(string $name, $default = '')
    {
        $this->name = $name;
        $this->default = $default;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        if ($value === '') {
            $notations[$this->name] = $this->default;
            return $notations;
        }

        $pattern = Pattern::of('^(?|"((?:[^"]+|\\\\.)*)"|\'((?:[^\']+|\\\\.)*)\')');
        $matcher = $pattern->match($value);
        if (in_array($value[0], ['"', "'"], true) && $matcher->test()) {
            $word = $matcher->first()->get(1);
        } else {
            $word = str_before($value, ' ');
        }
        $notations[$this->name] = $word;

        return $notations;
    }
}
