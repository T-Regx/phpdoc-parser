<?php
namespace Test\Fixtures;

use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\Assert;

trait ParseAssertion
{
    private function assertParses(string $phpDoc, array $expected): void
    {
        // when
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        Assert::assertSame($expected, $parser->parse($phpDoc));
    }
}
