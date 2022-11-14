<?php

namespace Jasny\PhpdocParser\Tests\Tag\PhpDocumentor;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag\PhpDocumentor\ExampleTag;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\PhpDocumentor\ExampleTag
 */
class ExampleTagTest extends TestCase
{
    /**
     * Provide data for testing 'process' method
     *
     * @return array
     */
    public function processProvider()
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
     * Test 'process' method
     *
     * @dataProvider processProvider
     */
    public function testProcess($value, $expected)
    {
        $tag = new ExampleTag('foo');
        $result = $tag->process(['some' => 'value'], $value);

        $this->assertSame($expected, $result);
    }

    /**
     * Provide data for testing 'process' method, in case when exception should be thrown
     *
     * @return array
     */
    public function processExceptionProvider()
    {
        return [
            'blank' => [''],
            'unclosed quote' => ['"some dir/and file.php'],
        ];
    }

    /**
     * Test 'process' method, if exception should be thrown
     *
     * @dataProvider processExceptionProvider
     */
    public function testProcessException($value)
    {
        $tag = new ExampleTag('foo');
        
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessageMatches("/Failed to parse '@foo .*?': invalid syntax/");
    
        $result = $tag->process(['some' => 'value'], $value);
    }
}
