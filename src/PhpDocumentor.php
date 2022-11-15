<?php
namespace Jasny\PhpdocParser;

use Jasny\PhpdocParser\Tag\DescriptionTag;
use Jasny\PhpdocParser\Tag\FlagTag;
use Jasny\PhpdocParser\Tag\ModifyTag;
use Jasny\PhpdocParser\Tag\MultiTag;
use Jasny\PhpdocParser\Tag\PhpDocumentor\ExampleTag;
use Jasny\PhpdocParser\Tag\PhpDocumentor\MethodTag;
use Jasny\PhpdocParser\Tag\PhpDocumentor\TypeTag;
use Jasny\PhpdocParser\Tag\PhpDocumentor\VarTag;
use Jasny\PhpdocParser\Tag\RegExpTag;
use Jasny\PhpdocParser\Tag\WordTag;

class PhpDocumentor implements PredefinedSetInterface
{
    public static function tags(?callable $fqsenConvertor = null): TagSet
    {
        return new TagSet([
            new FlagTag('api'),
            new RegExpTag('author', '/^(?:(?<name>(?:[^\<]\S*\s+)*[^\<]\S*)?\s*)?(?:\<(?<email>[^\>]+)\>)?/'),
            new DescriptionTag('copyright'),
            new WordTag('deprecated', true),
            new ExampleTag('example'),
            new FlagTag('ignore'),
            new FlagTag('internal'),
            new WordTag('link'),
            new MultiTag('methods', new MethodTag('method', $fqsenConvertor), 'name'),
            new WordTag('package'),
            new MultiTag('params', new VarTag('param', $fqsenConvertor), 'name'),
            new MultiTag('properties', new VarTag('property', $fqsenConvertor), 'name'),
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
            new VarTag('var', $fqsenConvertor)
        ]);
    }

    protected static function fqsen(Tag $tag, ?callable $fqsenConvertor): Tag
    {
        return isset($fqsenConvertor) ? new ModifyTag($tag, $fqsenConvertor) : $tag;
    }
}
