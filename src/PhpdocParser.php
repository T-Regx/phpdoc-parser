<?php
namespace Jasny\PhpdocParser;

class PhpdocParser
{
    /** @var TagSet */
    protected $tags;

    public function __construct(TagSet $tags)
    {
        $this->tags = $tags;
    }

    public function parse(string $doc, ?callable $callback = null): array
    {
        $notations = [];
        $rawNotations = $this->extractNotations($doc);
        $rawNotations = $this->joinMultilineNotations($rawNotations);

        foreach ($rawNotations as $item) {
            if (!isset($this->tags[$item['tag']])) {
                continue;
            }

            $notations = $this->tags[$item['tag']]->process($notations, $item['value'] ?? '');
        }

        if (isset($this->tags['summery'])) {
            $notations = $this->tags['summery']->process($notations, $doc);
        }

        if ($callback !== null) {
            $notations = $callback($notations);
        }

        return $notations;
    }

    protected function extractNotations(string $doc): array
    {
        $matches = null;

        $tag = '\s*@(?<tag>\S+)(?:\h+(?<value>\S.*?)|\h*)';
        $tagContinue = '(?:\040){2}(?<multiline_value>\S.*?)';
        $regex = '/^\s*(?:(?:\/\*)?\*)?(?:' . $tag . '|' . $tagContinue . ')(?:\s*\*\*\/)?\r?$/m';

        if (preg_match_all($regex, $doc, $matches, PREG_SET_ORDER)) {
            return $matches;
        }
        return [];
    }

    protected function joinMultilineNotations(array $rawNotations): array
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

                $result[$lastIdx]['value'] = trim($result[$lastIdx]['value'])
                    . ' ' . trim($item['multiline_value']);
            }
        }

        return $result;
    }

    protected function filterTagsNotations(array $rawNotations): array
    {
        for ($i = 0; $i < count($rawNotations); $i++) {
            if ($rawNotations[$i]['tag'] !== '') {
                return array_slice($rawNotations, $i);
            }
        }

        return [];
    }
}
