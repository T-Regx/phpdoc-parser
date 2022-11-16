<?php
namespace Test\Feature\tags\var_;

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
    public function shouldParseVariable()
    {
        // when, then
        $this->assertParses(resource('tags/var.txt'), [
            'var' => [
                'type'        => 'string',
                'name'        => 'name',
                'description' => 'Should contain a description',
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseVariableArrayString()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('var.array.string.txt'), [
            'var' => [
                'type'        => 'string[]',
                'name'        => 'name',
                'description' => 'An array of string objects.',
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseVariableArrayObject()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('var.array.object.txt'), [
            'var' => [
                'type'        => '\DateTime[]',
                'name'        => 'name',
                'description' => 'An array of DateTime objects.',
            ]
        ]);
    }

    /**
     * @test
     * @dataProvider notations
     */
    public function test(string $value, array $expected)
    {
        // when, then
        $this->assertParses("/** @var $value\n*/", $expected);
    }

    /**
     * @test
     * @dataProvider notations
     */
    public function testProcess2(string $value, array $expected)
    {
        // when, then
        $this->assertParses("/** @var $value */", $expected);
    }

    /**
     * @test
     * @dataProvider notations
     */
    public function testProcess3(string $value, array $expected)
    {
        // when, then
        $this->assertParses("/** @var $value \n*/", $expected);
    }

    public function notations(): array
    {
        return [
            [
                'int Some description here',
                [
                    'var' => [
                        'type'        => 'int',
                        'description' => 'Some description here'
                    ]
                ]
            ],
            [
                '$amount Some description here',
                [
                    'var' => [
                        'name'        => 'amount',
                        'description' => 'Some description here'
                    ]
                ]
            ],
            [
                'int $amount "some id"',
                [
                    'var' => [
                        'type' => 'int',
                        'name' => 'amount',
                        'id'   => 'some id'
                    ]
                ]
            ],
            [
                'int|string|Foo\Bar|null $amount "some id"',
                [
                    'var' => [
                        'type' => 'int|string|Foo\Bar|null',
                        'name' => 'amount',
                        'id'   => 'some id',
                    ]
                ]
            ]
        ];
    }

    /**
     * @test
     */
    public function shouldMapClassname()
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags(Functions::prepend('some_namespace\\')));
        // when
        $parsed = $parser->parse("/** @var Foo \$amount \"some id\" Some description here\n */");
        // then
        $expected = [
            'var' => [
                'type'        => 'some_namespace\Foo',
                'name'        => 'amount',
                'id'          => 'some id',
                'description' => 'Some description here'
            ]
        ];
        Assert::assertSame($expected, $parsed);
    }
}
