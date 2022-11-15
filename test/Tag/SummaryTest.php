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
    public function testProcess(string $doc, array $expected)
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
        $doc1 = "/**\n * Have summary here.\n */";
        $doc2 = "/**\n * Have summary here.\n * And a description\n * for a few lines\n * of words.\n */";
        $doc3 = "/**\n * Have summary here.\n *\n * And a description\n * for a few lines\n * of words.\n */";
        $doc4 = "/**\n * Have summary here.\n *\n * And a description\n *  for a few lines\n *  of words.\n */";
        $doc5 = "/**\n * Have summary here.\n *\n * And a description\n *  for a few lines\n *  of words.\n *\n * @param int\n * @return Foo\n */";
        $doc6 = "/**\n *\n */";
        $doc7 = "/**\n * @param int\n * @return Foo\n */";

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
