<?php
namespace Test\Feature\example;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseExampleFile()
    {
        // when, then
        $this->assertParses(resource('example.file.txt'), [
            'example' => [
                'location'    => 'example1.php',
                'description' => 'Counting in action.'
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseExampleLink()
    {
        // when, then
        $this->assertParses(resource('example.link.txt'), [
            'example' => [
                'location'    => 'https://example.com/example2.phps',
                'description' => 'Counting in action by a 3rd party.'
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseExampleString()
    {
        // when, then
        $this->assertParses(resource('example.string.txt'), [
            'example' => [
                'location'    => 'My Own Example.php',
                'description' => 'My counting.'
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldThrowForEmptyExample()
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@example ': invalid syntax");
        // when
        $parser->parse("/** @example \n*/");
    }

    /**
     * @test
     */
    public function shouldThrowForEmptyExampleNoEmptyLine()
    {
        $this->markTestIncomplete();
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@example ': invalid syntax");
        // when
        $parser->parse("/** @example */");
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function test(string $value, array $expected)
    {
        // when, then
        $this->assertParses("/** @example $value\n*/", $expected);
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testWhitespaceBeforeNewLine(string $value, array $expected)
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses("/** @example $value \n*/", $expected);
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testWhitespace(string $value, array $expected)
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses("/** @example $value */", $expected);
    }

    public function processProvider(): array
    {
        return [
            [
                'some_dir/and_file.php',
                ['example' => ['location' => 'some_dir/and_file.php']]
            ],
            [
                'some_dir/and_file.php 47',
                ['example' => ['location' => 'some_dir/and_file.php', 'start_line' => 47]]
            ],
            [
                'some_dir/and_file.php 47 39',
                ['example' => ['location' => 'some_dir/and_file.php', 'start_line' => 47, 'number_of_lines' => 39]]
            ],
            [
                '"some dir/and file.php" 47 39',
                ['example' => ['location' => 'some dir/and file.php', 'start_line' => 47, 'number_of_lines' => 39]]
            ],
            [
                '"some dir/and file.php" 47 And following description',
                ['example' => ['location' => 'some dir/and file.php', 'start_line' => 47, 'description' => 'And following description']]
            ],
            [
                '"some dir/and file.php" 47 39 And following description',
                ['example' => ['location' => 'some dir/and file.php', 'start_line' => 47, 'number_of_lines' => 39, 'description' => 'And following description']]
            ],
            [
                '"some dir/and file.php" And following description',
                ['example' => ['location' => 'some dir/and file.php', 'description' => 'And following description']]
            ],
        ];
    }
}
