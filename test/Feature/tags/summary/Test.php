<?php
namespace Test\Feature\tags\summary;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseSimpleSummary()
    {
        // when, then
        $this->assertParses(resource('tags/summary.txt'), [
            'summary' => 'Simple sentence'
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSimpleSummaryWithPeriod()
    {
        // when, then
        $this->assertParses(resource('tags/summary.period.txt'), [
            'summary' => 'Simple sentence.'
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSimpleSummaryAndDescription()
    {
        // when, then
        $this->assertParses(resource('tags/summary.twoSentences.txt'), [
            'summary'     => 'This is the first sentence.',
            'description' => 'This is the second sentence'
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSummaryInTwoLines()
    {
        // when, then
        $this->assertParses(resource('tags/summary.twoLines.txt'), [
            'summary' => 'Simple sentence',
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSummaryInThreeLines()
    {
        // when, then
        $this->assertParses(resource('tags/summary.threeLines.txt'), [
            'summary'     => 'Very simple sentence',
            'description' => 'Description',
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSummaryInTwoLinesWithPadding()
    {
        // when, then
        $this->assertParses(resource('tags/summary.twoLines.padding.txt'), [
            'summary' => 'Simple sentence',
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSummaryAndDescriptionInTheNextLine()
    {
        // when, then
        $this->assertParses(resource('tags/summary.description.txt'), [
            'summary'     => 'This is the first sentence',
            'description' => 'This is the second sentence'
        ]);
    }

    /**
     * @test
     */
    public function shouldParseSummaryPeriodAndDescriptionInTheNextLine()
    {
        // when, then
        $this->assertParses(resource('tags/summary.description.period.txt'), [
            'summary'     => 'This is the first sentence.',
            'description' => 'This is the second sentence.'
        ]);
    }

    /**
     * @test
     * @author Arnold Daniels
     * @dataProvider summaryPhpDocs
     */
    public function testProcess(string $doc, array $expected)
    {
        $this->assertParses($doc, $expected);
    }

    public function summaryPhpDocs(): array
    {
        $doc1 = "/**\n * Have summary here.\n */";
        $doc2 = "/**\n * Have summary here.\n * And a description\n * for a few lines\n * of words.\n */";
        $doc3 = "/**\n * Have summary here.\n *\n * And a description\n * for a few lines\n * of words.\n */";
        $doc4 = "/**\n * Have summary here.\n *\n * And a description\n *  for a few lines\n *  of words.\n */";
        $doc5 = "/**\n * Have summary here.\n *\n * And a description\n *  for a few lines\n *  of words.\n *\n */";
        $doc6 = "/**\n *\n */";

        return [
            [
                $doc1,
                ['summary' => 'Have summary here.']
            ],
            [
                $doc2,
                ['summary' => 'Have summary here.', 'description' => "And a description\nfor a few lines\nof words."]
            ],
            [
                $doc3,
                ['summary' => 'Have summary here.', 'description' => "And a description\nfor a few lines\nof words."]
            ],
            [
                $doc4,
                ['summary' => 'Have summary here.', 'description' => "And a description\nfor a few lines\nof words."]
            ],
            [
                $doc5,
                ['summary' => 'Have summary here.', 'description' => "And a description\nfor a few lines\nof words."]
            ],
            [
                $doc6,
                []
            ],
        ];
    }
}
