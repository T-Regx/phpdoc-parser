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
        $notations = [];
        $rawNotations = $this->extractNotations($docBlock);
        $rawNotations = $this->joinMultilineNotations($rawNotations);

        foreach ($rawNotations as $item) {
            if ($this->tagSet->tagExists($item['tag'])) {
                $notations = $this->tagSet->tagByName($item['tag'])->process($notations, $item['value'] ?? '');
            }
        }

        if ($this->tagSet->tagExists('summary')) {
            $notations = $this->tagSet->tagByName('summary')->process($notations, $docBlock);
        }

        return $notations;
    }

    private function extractNotations(string $doc): array
    {
        $tag = '\s*@(?<tag>\S+)(?:\h+(?<value>\S.*?)|\h*)';
        $tagContinue = '(?:\040){2}(?<multiline_value>\S.*?)';
        $regex = '/^\s*(?:(?:\/\*)?\*)?(?:' . $tag . '|' . $tagContinue . ')(?:\s*\*\*\/)?\r?$/m';

        if (preg_match_all($regex, $doc, $matches, PREG_SET_ORDER)) {
            return $matches;
        }
        return [];
    }

    private function joinMultilineNotations(array $rawNotations): array
    {
        $result = [];
        $tagsNotations = $this->filterTagsNotations($rawNotations);

        foreach ($tagsNotations as $item) {
            if ($item['tag'] !== '') {
                $result[] = $item;
            } else {
                $lastIdx = count($result) - 1;
                if (!isset($result[$lastIdx]['value'])) {
                    $result[$lastIdx]['value'] = '';
                }
                $result[$lastIdx]['value'] = trim($result[$lastIdx]['value']) . ' ' . trim($item['multiline_value']);
            }
        }

        return $result;
    }

    private function filterTagsNotations(array $rawNotations): array
    {
        for ($i = 0; $i < count($rawNotations); $i++) {
            if ($rawNotations[$i]['tag'] !== '') {
                return array_slice($rawNotations, $i);
            }
        }
        return [];
    }
}
