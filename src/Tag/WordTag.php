<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;
use function Jasny\expect_type;
use function Jasny\str_before;

/**
 * Only use the first word after the tag, ignoring the rest
 */
class WordTag implements Tag
{
    /** @var string */
    private $name;
    /** @var string|bool */
    protected $default;

    /**
     * @param string $name
     * @param string|bool $default
     */
    public function __construct(string $name, $default = '')
    {
        $this->name = $name;

        expect_type($default, ['string', 'bool']);
        $this->default = $default;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function process(array $notations, string $value): array
    {
        if ($value === '') {
            $notations[$this->name] = $this->default;
            return $notations;
        }

        $matches = [];
        $quoted = in_array($value[0], ['"', '\''], true) &&
            preg_match('/^(?|"((?:[^"]+|\\\\.)*)"|\'((?:[^\']+|\\\\.)*)\')/', $value, $matches);

        $word = $quoted ? $matches[1] : str_before($value, ' ');
        $notations[$this->name] = $word;

        return $notations;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
