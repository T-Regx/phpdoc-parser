<?php
namespace Jasny\PhpdocParser;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\Tag\AuthorTag;
use Jasny\PhpdocParser\Tag\DescriptionTag;
use Jasny\PhpdocParser\Tag\ExampleTag;
use Jasny\PhpdocParser\Tag\FlagTag;
use Jasny\PhpdocParser\Tag\MethodTag;
use Jasny\PhpdocParser\Tag\MultiTag;
use Jasny\PhpdocParser\Tag\TypeTag;
use Jasny\PhpdocParser\Tag\VarTag;
use Jasny\PhpdocParser\Tag\WordTag;

class PhpDocumentor
{
    public static function tags(?callable $fullyQualifiedName = null): TagSet
    {
        return self::tagsWithClassName(ClassName::ofNullable($fullyQualifiedName));
    }

    private static function tagsWithClassName(ClassName $className): TagSet
    {
        return new TagSet([
            new FlagTag('api'),
            new AuthorTag('author'),
            new DescriptionTag('copyright'),
            new WordTag('deprecated', true),
            new ExampleTag('example'),
            new FlagTag('ignore'),
            new FlagTag('internal'),
            new WordTag('link'),
            new MultiTag('methods', new MethodTag('method', $className)),
            new WordTag('package'),
            new MultiTag('params', new VarTag('param', [], $className)),
            new MultiTag('properties', new VarTag('property', [], $className)),
            new MultiTag('properties', new VarTag('property-read', ['read_only' => true], $className)),
            new MultiTag('properties', new VarTag('property-write', ['write_only' => true], $className)),
            new TypeTag('return', $className),
            new WordTag('see'),
            new WordTag('since'),
            new MultiTag('throws', new TypeTag('throws', $className)),
            new DescriptionTag('todo'),
            new TypeTag('uses', $className),
            new TypeTag('used-by', $className),
            new VarTag('var', [], $className),
            new WordTag('version'),
        ]);
    }
}
