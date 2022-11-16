<?php
namespace Jasny\PhpdocParser;

class PhpdocParser
{
    /** @var TagSet */
    private $tagSet;

    public function __construct(TagSet $tagSet)
    {
        $this->tagSet = $tagSet;
    }

    public function parse(string $docBlock): array
    {
        [$summaryLines, $tagList] = $this->summaryLinesAndTagList($docBlock);
        $notations = $this->summaryAndDescription($this->summaryAsString($summaryLines));
        foreach ($tagList as ['tag' => $tagName, 'value' => $value]) {
            $notations = $this->tagSet->tagByName($tagName)->process($notations, $value ?? '');
        }
        return $notations;
    }

    private function summaryLinesAndTagList(string $docBlock): array
    {
        [$summaryLines, $joinedBatches] = $this->summaryLinesAndTagLines($this->tagsAndLines($docBlock));
        return [$summaryLines, $this->tagList($joinedBatches)];
    }

    private function tagsAndLines(string $docBlock): array
    {
        $regex = '/^
              \s*
              (?:
                (?:\/\*)?
                \*
              )
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
            $/xm';

        if (preg_match_all($regex, $docBlock, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) === 0) {
            throw new \RuntimeException("Failed to parse");
        }

        $result = [];

        foreach ($matches as $match) {
            $tag = $this->match($match['tag']);
            $value = $this->match($match['value'] ?? ['', -1]);
            $multiline = $this->match($match['multiline'] ?? ['', -1]);

            if ($tag !== null) {
                $result[] = ['tag' => $tag, 'value' => $value];
            }

            if ($multiline !== null) {
                $result[] = ['text' => trim($multiline)];
            }
        }
        return $result;
    }

    private function match(array $match): ?string
    {
        if ($match[1] === -1) {
            return null;
        }
        return $match[0];
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
        $splitElements = \preg_split('/(\.|\n\n)/', $summaryAndDescription, 2, PREG_SPLIT_DELIM_CAPTURE);
        if (\count($splitElements) === 3) {
            [$summary, $delimiter, $description] = $splitElements;
            $cleanSummary = \trim(\preg_replace('/\s+/', ' ', $summary . $delimiter));
            if ($description === '') {
                return ['summary' => $cleanSummary];
            }
            return [
                'summary'     => $cleanSummary,
                'description' => \trim($description)
            ];
        }
        return [
            'summary' => trim(\preg_replace('/\s+/', ' ', $splitElements[0]))
        ];
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
