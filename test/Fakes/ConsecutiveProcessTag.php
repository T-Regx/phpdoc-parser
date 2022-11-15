<?php
namespace Test\Fakes;

use Jasny\PhpdocParser\Tag;

class ConsecutiveProcessTag implements Tag
{
    /** @var string */
    private $name;
    /** @var Tag[] */
    private $calls;

    public function __construct(string $name, array $calls)
    {
        $this->name = $name;
        $this->calls = $calls;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $current = \current($this->calls);
        \next($this->calls);
        return $current->process($notations, $value);
    }
}
