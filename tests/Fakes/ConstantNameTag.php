<?php
namespace Jasny\PhpdocParser\Tests\Fakes;

use Jasny\PhpdocParser\Tag;
use PHPUnit\Framework\Assert;

class ConstantNameTag implements Tag
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        Assert::fail("Failed to assert that Tag.process() wasn't called");
    }
}
