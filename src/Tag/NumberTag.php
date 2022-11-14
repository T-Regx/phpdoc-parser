<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use function Jasny\expect_type;

/**
 * Only use the first word (that should be a number) after the tag, ignoring the rest
 */
class NumberTag extends AbstractTag
{
    /** @var string */
    public $type;

    /** @var int|float */
    public $min;

    /** @var int|float */
    public $max;

    /**
     * @param string $name
     * @param string $type ('int', 'float')
     * @param int|float $min
     * @param int|float $max
     */
    public function __construct(string $name, string $type = 'int', $min = 0, $max = INF)
    {
        if (!in_array($type, ['int', 'integer', 'float'], true)) {
            throw new PhpdocException("NumberTag should be of type int or float, $type given");
        }

        expect_type($min, ['int', 'float']);
        expect_type($max, ['int', 'float']);

        if ($min > $max) {
            throw new PhpdocException("Min value (given $min) should be less than max (given $max)");
        }

        parent::__construct($name);

        $this->type = $type;
        $this->min = $min;
        $this->max = $max;
    }

    public function process(array $notations, string $value): array
    {
        [$word] = explode(' ', $value, 2);

        if (!is_numeric($word)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $word': not a number");
        }

        if ($word < $this->min) {
            throw new PhpdocException("Parsed value $word should be greater then min value {$this->min}");
        }

        if ($word > $this->max) {
            throw new PhpdocException("Parsed value $word should be less then max value {$this->max}");
        }

        if (in_array($this->type, ['int', 'integer'], true)) {
            $word = (int)$word;
        }

        $notations[$this->name] = $word + 0;

        return $notations;
    }
}
