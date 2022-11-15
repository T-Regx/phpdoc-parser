<?php
namespace Jasny\PhpdocParser\Tests\Fakes;

use Jasny\PhpdocParser\Tag;
use PHPUnit\Framework\Assert;

class EmptyNotationsTag implements Tag
{
    /** @var string */
    private $expectedValue;
    /** @var array */
    private $processed;
    /** @var string|null */
    private $name;

    public function __construct(string $expectedValue, array $processed, string $name = null)
    {
        $this->expectedValue = $expectedValue;
        $this->processed = $processed;
        $this->name = $name;
    }

    public function getName(): string
    {
        if ($this->name === null) {
            Assert::fail("Failed to assert that ProcessedTag.getName() wasn't called");
        }
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        Assert::assertSame([], $notations);
        Assert::assertSame($this->expectedValue, $value);
        return $this->processed;
    }
}
