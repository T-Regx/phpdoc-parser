<?php
namespace Jasny\PhpdocParser\Tag;

class ArrayTag extends AbstractArrayTag
{
    protected function splitValue(string $value): array
    {
        $regexp = '/(?<=^|,)\s*(?:\'(?:[^\']++|\\\\.)*\'|(?:"(?:[^"]++|\\\\.)*"|[^,\'"]+|[\'"])+)/';
        preg_match_all($regexp, $value, $matches, PREG_PATTERN_ORDER); // regex can't fail

        return $matches[0];
    }
}
