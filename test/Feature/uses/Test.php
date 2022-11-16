<?php
namespace Test\Feature\uses;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\Functions;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseUses()
    {
        // when, then
        $this->assertParses(resource('tags/uses.txt'), ['uses' => ['type' => 'MyClass::$items']]);
    }

    /**
     * @test
     */
    public function shouldParseUsedBy()
    {
        // when, then
        $this->assertParses(resource('tags/used-by.txt'), ['used-by' => ['type' => 'MyClass::$items']]);
    }

    /**
     * @test
     */
    public function shouldParseUsesDescription()
    {
        // when, then
        $this->assertParses(resource('tags/uses.description.txt'), [
            'uses' => [
                'type'        => 'MyClass::$items',
                'description' => 'to retrieve the count from.'
            ]
        ]);
    }

    /**
     * @test
     */
    public function testMalformed(): void
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@uses': tag value should not be empty");
        // when
        $parser->parse("/** @uses\n*/");
    }

    /**
     * @test
     * @dataProvider notations
     */
    public function test(string $value, array $expected)
    {
        // when, then
        $this->assertParses("/** @uses $value\n*/", $expected);
    }

    public function notations(): array
    {
        return [
            ['FooType', ['uses' => ['type' => 'FooType']]],
            ['FooType Some description here', ['uses' => ['type' => 'FooType', 'description' => 'Some description here']]],
            ['Bar\Foo\Type', ['uses' => ['type' => 'Bar\Foo\Type']]],
            ['Bar\Foo\Type Some description here', ['uses' => ['type' => 'Bar\Foo\Type', 'description' => 'Some description here']]],
        ];
    }

    /**
     * @test
     */
    public function shouldMapClassname()
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags(Functions::prepend("Bar\\")));
        // when
        $parsed = $parser->parse("/** @uses FooType Some description here\n */");
        // then
        $expected = ['uses' => ['type' => 'Bar\FooType', 'description' => 'Some description here']];
        Assert::assertSame($expected, $parsed);
    }
}
