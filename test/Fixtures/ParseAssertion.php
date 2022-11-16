<?php
namespace Test\Fixtures;

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\Assert;

trait ParseAssertion
{
    private function assertParses(string $phpDoc, array $expected): void
    {
        $parser = new PHPDocParser(PhpDocumentor::tags());
        Assert::assertSame($expected, $parser->parse($phpDoc));
    }
}
