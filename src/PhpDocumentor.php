<?php
namespace Jasny\PhpdocParser;

use Jasny\PhpdocParser\Tag\AuthorTag;
use Jasny\PhpdocParser\Tag\DescriptionTag;
use Jasny\PhpdocParser\Tag\ExampleTag;
use Jasny\PhpdocParser\Tag\FlagTag;
use Jasny\PhpdocParser\Tag\MethodTag;
use Jasny\PhpdocParser\Tag\ModifyTag;
use Jasny\PhpdocParser\Tag\MultiTag;
use Jasny\PhpdocParser\Tag\SummaryAndDescription;
use Jasny\PhpdocParser\Tag\TypeTag;
use Jasny\PhpdocParser\Tag\VarTag;
use Jasny\PhpdocParser\Tag\WordTag;

class PhpDocumentor
{
    public static function tags(?callable $fqsenConvertor = null): TagSet
    {
        return new TagSet([
            new SummaryAndDescription(),
            new FlagTag('api'),
            new AuthorTag('author'),
            new DescriptionTag('copyright'),
            new WordTag('deprecated', true),
            new ExampleTag('example'),
            new FlagTag('ignore'),
            new FlagTag('internal'),
            new WordTag('link'),
            new MultiTag('methods', new MethodTag('method', $fqsenConvertor), 'name'),
            new WordTag('package'),
            new MultiTag('params', new VarTag('param', $fqsenConvertor, []), 'name'),
            new MultiTag('properties', new VarTag('property', $fqsenConvertor, []), 'name'),
            new MultiTag(
                'properties',
                new VarTag('property-read', $fqsenConvertor, ['read_only' => true]),
                'name'
            ),
            new MultiTag(
                'properties',
                new VarTag('property-write', $fqsenConvertor, ['write_only' => true]),
                'name'
            ),
            new TypeTag('return', $fqsenConvertor),
            self::fqsen(new WordTag('see'), $fqsenConvertor),
            new WordTag('since'),
            new MultiTag('throws', new TypeTag('throws', $fqsenConvertor)),
            new DescriptionTag('todo'),
            new TypeTag('uses', $fqsenConvertor),
            new TypeTag('used-by', $fqsenConvertor),
            new VarTag('var', $fqsenConvertor, []),
            new WordTag('version'),
        ]);
    }

    private static function fqsen(Tag $tag, ?callable $fqsenConvertor): Tag
    {
        if (isset($fqsenConvertor)) {
            return new ModifyTag($tag, $fqsenConvertor);
        }
        return $tag;
    }
}
