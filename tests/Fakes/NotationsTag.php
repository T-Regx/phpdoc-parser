<?php
namespace Jasny\PhpdocParser\Tests\Fakes;

use Jasny\PhpdocParser\Tag;
use PHPUnit\Framework\Assert;

class NotationsTag implements Tag
{
    /** @var array */
    private $expectedNotations;
    /** @var string */
    private $expectedValue;
    /** @var array */
    private $processed;
    /** @var string|null */
    private $name;

    public function __construct(array $expectedNotations, string $expectedValue, array $processed, string $name = null)
    {
        $this->expectedNotations = $expectedNotations;
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
        Assert::assertSame($this->expectedNotations, $notations);
        Assert::assertSame($this->expectedValue, $value);
        return $this->processed;
    }
}
