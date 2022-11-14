<?php

namespace Jasny\PhpdocParser\Tests\Tag;

use Jasny\PhpdocParser\Tag\Summary;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\PhpdocParser\Tag\Summary
 */
class SummaryTest extends TestCase
{
    /**
     * @test
     */
    public function testGetName()
    {
        // given
        $tag = new Summary();
        // when, then
        $this->assertSame('summery', $tag->getName());
    }

    /**
     * @test
     * @dataProvider processProvider
     */
    public function testProcess($doc, $expected)
    {
        // given
        $tag = new Summary();
        // when
        $result = $tag->process(['some' => 'value'], $doc);
        // then
        $this->assertSame($expected, $result);
    }

    public function processProvider(): array
    {
        $doc1 = <<<DOC
/**
 * Have summery here.
 */
DOC;

        $doc2 = <<<DOC
/**
 * Have summery here.
 * And a description
 * for a few lines
 * of words.
 */
DOC;

        $doc3 = <<<DOC
/**
 * Have summery here.
 *
 * And a description
 * for a few lines
 * of words.
 */
DOC;

        $doc4 = <<<DOC
/**
 * Have summery here.
 *
 * And a description
 *  for a few lines
 *  of words.
 */
DOC;

        $doc5 = <<<DOC
/**
 * Have summery here.
 *
 * And a description
 *  for a few lines
 *  of words.
 *
 * @param int
 * @return Foo
 */
DOC;

        $doc6 = <<<DOC
/**
 *
 */
DOC;

        $doc7 = <<<DOC
/**
 * @param int
 * @return Foo
 */
DOC;

        return [
            [
                $doc1,
                ['some' => 'value', 'summery' => 'Have summery here.', 'description' => "Have summery here."]
            ],
            [
                $doc2,
                ['some' => 'value', 'summery' => 'Have summery here.', 'description' => "Have summery here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc3,
                ['some' => 'value', 'summery' => 'Have summery here.', 'description' => "Have summery here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc4,
                ['some' => 'value', 'summery' => 'Have summery here.', 'description' => "Have summery here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc5,
                ['some' => 'value', 'summery' => 'Have summery here.', 'description' => "Have summery here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc6,
                ['some' => 'value']
            ],
            [
                $doc7,
                ['some' => 'value']
            ],
        ];
    }
}
