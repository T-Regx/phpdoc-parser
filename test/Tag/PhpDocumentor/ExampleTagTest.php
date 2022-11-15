<?php
namespace Test\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\PhpDocumentor\ExampleTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\PhpDocumentor\ExampleTag
 */
class ExampleTagTest extends TestCase
{
    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess(string $value, array $expected)
    {
        // given
        $tag = new ExampleTag('foo');
        // when
        $result = $tag->process(['some' => 'value'], $value);
        // then
        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        return [
            '@example some_dir/and_file.php' => [
                'some_dir/and_file.php',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some_dir/and_file.php'
                    ]
                ]
            ],
            '@example some_dir/and_file.php 47' => [
                'some_dir/and_file.php 47',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some_dir/and_file.php',
                        'start_line' => 47
                    ]
                ]
            ],
            '@example some_dir/and_file.php 47 39' => [
                'some_dir/and_file.php 47 39',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some_dir/and_file.php',
                        'start_line' => 47,
                        'number_of_lines' => 39
                    ]
                ]
            ],
            '@example "some dir/and file.php" 47 39' => [
                '"some dir/and file.php" 47 39',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some dir/and file.php',
                        'start_line' => 47,
                        'number_of_lines' => 39
                    ]
                ]
            ],
            '@example "some dir/and file.php" 47 And following description' => [
                '"some dir/and file.php" 47 And following description',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some dir/and file.php',
                        'start_line' => 47,
                        'description' => 'And following description'
                    ]
                ]
            ],
            '@example "some dir/and file.php" 47 39 And following description' => [
                '"some dir/and file.php" 47 39 And following description',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some dir/and file.php',
                        'start_line' => 47,
                        'number_of_lines' => 39,
                        'description' => 'And following description'
                    ]
                ]
            ],
            '@example "some dir/and file.php" And following description' => [
                '"some dir/and file.php" And following description',
                [
                    'some' => 'value',
                    'foo' => [
                        'location' => 'some dir/and file.php',
                        'description' => 'And following description'
                    ]
                ]
            ],
        ];
    }

    /**
     * @test
     * @dataProvider processExceptionProvider
     */
    public function testProcessException(string $value)
    {
        // given
        $tag = new ExampleTag('foo');
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessageMatches("/Failed to parse '@foo .*?': invalid syntax/");
        // when
        $tag->process(['some' => 'value'], $value);
    }

    public function processExceptionProvider(): array
    {
        return [
            'blank' => [''],
            'unclosed quote' => ['"some dir/and file.php'],
        ];
    }
}
