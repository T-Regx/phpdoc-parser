<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\Tag;

class CustomTag implements Tag
{
    /** @var string */
    private $name;
    /** @var callable */
    protected $process;

    public function __construct(string $name, callable $process)
    {
        $this->name = $name;
        $this->process = $process;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        return call_user_func($this->process, $notations, $value);
    }
}
