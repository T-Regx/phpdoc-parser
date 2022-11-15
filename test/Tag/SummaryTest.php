<?php
namespace Test\Tag;

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
        $this->assertSame('summary', $tag->getName());
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
 * Have summary here.
 */
DOC;

        $doc2 = <<<DOC
/**
 * Have summary here.
 * And a description
 * for a few lines
 * of words.
 */
DOC;

        $doc3 = <<<DOC
/**
 * Have summary here.
 *
 * And a description
 * for a few lines
 * of words.
 */
DOC;

        $doc4 = <<<DOC
/**
 * Have summary here.
 *
 * And a description
 *  for a few lines
 *  of words.
 */
DOC;

        $doc5 = <<<DOC
/**
 * Have summary here.
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
                ['some' => 'value', 'summary' => 'Have summary here.', 'description' => "Have summary here."]
            ],
            [
                $doc2,
                ['some' => 'value', 'summary' => 'Have summary here.', 'description' => "Have summary here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc3,
                ['some' => 'value', 'summary' => 'Have summary here.', 'description' => "Have summary here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc4,
                ['some' => 'value', 'summary' => 'Have summary here.', 'description' => "Have summary here.\nAnd a description\nfor a few lines\nof words."]
            ],
            [
                $doc5,
                ['some' => 'value', 'summary' => 'Have summary here.', 'description' => "Have summary here.\nAnd a description\nfor a few lines\nof words."]
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
