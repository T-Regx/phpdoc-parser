<?php
namespace Jasny\PhpdocParser;

use TRegx\CleanRegex\Pattern;

class PhpdocParser
{
    /** @var TagSet */
    private $tagSet;
    /** @var Pattern */
    private $docBlockPattern;

    public function __construct(TagSet $tagSet)
    {
        $this->tagSet = $tagSet;
        $this->docBlockPattern = Pattern::of('^\s*/\*\*(.*)\*/\s*$', 's');
    }

    public function parse(string $docBlock): array
    {
        $matcher = $this->docBlockPattern->match($docBlock);
        if ($matcher->fails()) {
            throw new PhpdocException("Failed to parse");
        }
        [$summaryLines, $tagList] = $this->summaryLinesAndTagList($matcher->first()->get(1));
        $notations = $this->summaryAndDescription($this->summaryAsString($summaryLines));
        foreach ($tagList as ['tag' => $tagName, 'value' => $value]) {
            $notations = $this->tagSet->tagByName($tagName)->process($notations, trim($value ?? ''));
        }
        return $notations;
    }

    private function summaryLinesAndTagList(string $docBlock): array
    {
        if (\strPos($docBlock, '*/') === false) {
            [$summaryLines, $joinedBatches] = $this->summaryLinesAndTagLines($this->tagsAndLines($docBlock));
            return [$summaryLines, $this->tagList($joinedBatches)];
        }
        throw new PhpdocException("Failed to parse");
    }

    private function tagsAndLines(string $docBlock): array
    {
        $regex = Pattern::of('(*ANYCRLF)^
              \s*
              \*?
              (?:
                \s*?
                @(?<tag>\S+)
                (?:
                  \h+(?<value>\S.*?)
                  |
                  \h*
                )
                |
                \ *?
                (?<multiline>\S?.*?)
              )
              (?:\s*\*?\*\/)?
            $', 'xm');

        $matcher = $regex->match($docBlock);

        if ($matcher->fails()) {
            throw new PhpdocException("Failed to parse");
        }

        $result = [];
        foreach ($matcher as $match) {
            if ($match->matched('tag')) {
                $result[] = [
                    'tag'   => $match->get('tag'),
                    'value' => $match->group('value')->or('')
                ];
            }
            if ($match->matched('multiline')) {
                $result[] = ['text' => \trim($match->get('multiline'))];
            }
        }
        return $result;
    }

    private function summaryLinesAndTagLines(array $tagsAndLines): array
    {
        $batches = \array_map([$this, 'unwrapFirstTextBatch'], $this->textItemsPrepended($tagsAndLines));
        if ($batches[0][0] === 'text') {
            $summaryLines = array_shift($batches)[1];
            return [$summaryLines, $batches];
        }
        return [[], $batches];
    }

    private function unwrapFirstTextBatch(array $batch): array
    {
        if (\array_key_exists('text', $batch[0])) {
            $texts = [];
            foreach ($batch as $line) {
                $texts[] = $line['text'];
            }
            return ['text', $texts];
        }
        return ['tag', $batch];
    }

    private function summaryAsString(array $summaryLines): string
    {
        return \trim(\implode("\n", \array_map('trim', $summaryLines)));
    }

    private function summaryAndDescription(string $summaryAndDescription): array
    {
        if ($summaryAndDescription === '') {
            return [];
        }
        $pattern = Pattern::of('(\.|\n\n)');
        $splitElements = $pattern->splitStart($summaryAndDescription, 1);
        if (\count($splitElements) === 3) {
            [$summary, $delimiter, $description] = $splitElements;
            $cleanSummary = $this->oneLineSummary($summary . $delimiter);
            if ($description === '') {
                return ['summary' => $cleanSummary];
            }
            return [
                'summary'     => $cleanSummary,
                'description' => \trim($description)
            ];
        }
        return [
            'summary' => $this->oneLineSummary($splitElements[0])
        ];
    }

    private function oneLineSummary(string $summary): string
    {
        return \trim(\pattern('\s+')->replace($summary)->with(' '));
    }

    private function tagList(array $joinedBatches): array
    {
        return \array_map(function (array $array) {
            if (count($array[1]) === 1) {
                return $array[1][0];
            }
            $tags = $array[1];
            $firstTag = array_shift($tags);
            if (\array_key_exists('value', $firstTag)) {
                $firstTag['value'] .= "\n";
            } else {
                $firstTag['value'] = '';
            }
            foreach ($tags as ['text' => $value]) {
                $firstTag['value'] .= $value . "\n";
            }
            return $firstTag;
        }, $joinedBatches);
    }

    private function textItemsPrepended(array $tagsAndLines): array
    {
        $batches = [];
        $currentBatch = [];
        foreach ($tagsAndLines as $line) {
            if (!\array_key_exists('text', $line)) {
                if (!empty($currentBatch)) {
                    $batches[] = $currentBatch;
                    $currentBatch = [];
                }
            }
            $currentBatch[] = $line;
        }
        if (!empty($currentBatch)) {
            $batches[] = $currentBatch;
        }
        return $batches;
    }

}
