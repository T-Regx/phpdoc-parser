<?php

declare(strict_types=1);

namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;

/**
 * Tag value represents a map (aka associative array).
 */
class MapTag extends AbstractArrayTag
{
    protected function splitValue(string $value): array
    {
        $regexpKey = '(?:\'(?:[^\']++|\\\\.)*\'|"(?:[^"]++|\\\\.)*"|[^,\'"\=]++|[\'"])*';
        $regexpValue = '\'(?:[^\']++|\\\\.)*\'|(?:"(?:[^"]++|\\\\.)*"|[^,\'"]+|[\'"])*';
        $regexp = '/(?<=^|,)(?:(?<key>' . $regexpKey . ')\s*=\s*)?(?<value>' . $regexpValue . ')\s*/';

        preg_match_all($regexp, $value, $matches, PREG_PATTERN_ORDER); // regex can't fail

        $keys = preg_replace('/^\s*(["\']?)(.*?)\1\s*$/', '$2', $matches['key']);

        foreach ($keys as $pos => $key) {
            if ($key === '') {
                $item = trim($matches['value'][$pos]);
                throw new PhpdocException("Failed to parse '@{$this->name} {$value}': no key for value '$item'");
            }
        }

        return array_combine($keys, $matches['value']);
    }
}
