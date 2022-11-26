<p align="center"><a href="https://t-regx.com/"><img src="t.regx.png"></a></p>

# PhpDoc parser

Lightweight parser of PhpDoc.

[![Build Status](https://github.com/T-Regx/phpdoc-parser/workflows/build/badge.svg?branch=master)](https://github.com/T-Regx/phpdoc-parser/actions/)
[![Coverage Status](https://coveralls.io/repos/github/T-Regx/phpdoc-parser/badge.svg?branch=master)](https://coveralls.io/github/T-Regx/phpdoc-parser?branch=master)
[![Repository Size](https://github-size-badge.herokuapp.com/T-Regx/phpdoc-parser.svg)](https://github.com/T-Regx/phpdoc-parser)
[![License](https://img.shields.io/github/license/T-Regx/phpdoc-parser.svg)](https://github.com/T-Regx/phpdoc-parser/blob/master/LICENSE)
[![Composer lock](https://img.shields.io/badge/.lock-uncommited-green.svg)](https://github.com/T-Regx/phpdoc-parser)

[![PHP Version](https://img.shields.io/badge/PHP-7.1-blue.svg)][1]
[![PHP Version](https://img.shields.io/badge/PHP-7.2-blue.svg)][1]
[![PHP Version](https://img.shields.io/badge/PHP-7.3-blue.svg)][1]
[![PHP Version](https://img.shields.io/badge/PHP-7.4-blue.svg)][1]
[![PHP Version](https://img.shields.io/badge/PHP-8.0-blue.svg)][1]
[![PHP Version](https://img.shields.io/badge/PHP-8.1-blue.svg)][1]

[1]: https://github.com/T-Regx/phpdoc-parser/runs/2375602376

[![PRs Welcome](https://img.shields.io/badge/PR-welcome-brightgreen.svg?style=popout)](http://makeapullrequest.com)

1. [Installation](#installation)
    * [Composer](#installation)
2. [Usage](#usage)
3. [Tags](#tags)
4. [Tag classes](#tag-classes)
5. [FQSEN Resolve](#fqsen-resolver)
6. [Current limitations](#current-limitations)
7. [Fixed from the original](#fixed-from-the-original)

# Installation

Installation for PHP 7.1 and later:

```bash
composer require rawr/phpdoc-parser
```

## Usage

```php
/**
 * The description of foo. This function does a lot of thing
 *   which are described here.
 *
 * Some more text here.
 *
 * @important
 * @uses FooReader
 * @internal Why this isn't part of the API.
 *   Multi-line is supported.
 *
 * @param string|callable $first   This is the first param
 * @param int             $second  The second one
 * @return void
 * @throws InvalidArgumentException
 * @throws DoaminException if first argument is not found
 */
function foo($first, int $second)
{
   // ...
}
```

Parse annotations

```php
use Jasny\PHPDocParser\PHPDocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use Jasny\PHPDocParser\Tag\FlagTag;

$doc = (new ReflectionFunction('foo'))->getDocComment();

$customTags = [
    new FlagTag('important')
];
$tags = PhpDocumentor::tags()->with($customTags);

$parser = new PHPDocParser($tags);
$meta = $parser->parse($doc);
```

The result is the following:

```php
[
    'summery' => "The description of foo.",
    'description' => "This function does a lot of thing which are described here.\n\nSome more text.",
    'important' => true,
    'uses' => 'FooReader',
    'internal' => "Why this isn't part of the API. Mutlti-line is supported",
    'params' => [
        'first' => [
            'type' => "string|callable",
            'name' => "first",
            'description' => "This is the first parm"
        ],
        'second' => [
            'type' => "int",
            'name' => "second",
        ]
    ],
    'return' => 'void'
]
```

## Tags

The following tags are already included in `PhpDocumentor::tags()`:

* `@api`
* `@author`
* `@copyright`
* `@deprecated`
* `@example`
* `@ignore`
* `@internal`
* `@link`
* `@method` (all methods will be grouped in `methods` array)
* `@package`
* `@param` (all params will be grouped in `params` array)
* `@property` (all properties will be grouped in `properties` array)
* `@property-read` (also in `properties` array)
* `@property-write` (also in `properties` array)
* `@return`
* `@see`
* `@since`
* `@throws` (all exceptions will be grouped in `throws` array)
* `@todo`
* `@uses`
* `@used-by`
* `@var`

So if you only need to parse those tags, you can simply do:

```php
//$doc = ...; Get doc-comment string from reflection

$tags = PhpDocumentor::tags();
$parser = new PhpdocParser($tags);
$meta = $parser->parse($doc);
```

## Tag classes

Here's a list of available tags classes, that should cover most of the use cases:

* `Summery`
* `ArrayTag`
* `CustomTag`
* `DescriptionTag`
* `ExampleTag`
* `FlagTag`
* `MapTag`
* `MethodTag`
* `ModifyTag`
* `MultiTag`
* `NumberTag`
* `RegExpTag`
* `VarTag`
* `WordTag`

The following function is used in tags documentation, for short reference to parsing:

```php
function getNotations(string $doc, array $tags = []) {
    $tags = PhpDocumentor::tags()->add($tags);

    $parser = new PhpdocParser($tags);
    $notations = $parser->parse($doc);

    return $notations;
}
```

## FQSEN Resolver

FQSEN stands for `Fully Qualified Structural Element Name`. FQSEN convertor is used to expand class name or function name to fully unique name (so with full
namespace). For example, `Foo` can be converted to `Zoo\\Foo\\Bar`.

Such convertors are used in this lib. Some tags, that deal with variable types, or classes names, support adding them as a constructor parameter.

For example, `TypeTag`, that can be used for parsing `@return` tag, has the following constructor: `TypeTag($name, $fqsenConvertor = null)`. If provided,
convertor expands the type, given as type of returned value in doc-comment. If ommited, the type will stay as it is in doc-comment.

Convertor can be provided in one of two ways:

* `$tags = PhpDocumentor::tags($fn)` - for all the tags, predefined in `PhpDocumentor::tags()`
* `$tags = $tags->add(new TypeTag('footag', $fn))` - for all the tags, that are explicitly added to predefined, it should be passed as a constructor parameter (
  if it is supported by constructor).

After that create the parser from the tags as `$parser = new PhpdocParser($tags)`.

The resolver function should accept a class name and return an expanded name.

### Example

This example uses [phpDocumentor/TypeResolver](https://github.com/phpDocumentor/TypeResolver).

```php
$reflection = new ReflectionClass('\My\Example\Classy');

$contextFactory = new \phpDocumentor\Reflection\Types\ContextFactory();
$context = $contextFactory->createFromReflector($reflection);

$resolver = new \phpDocumentor\Reflection\FqsenResolver();
$fn = fn(string $class): string => $resolver->resolve($class, $context);

$tags = PhpDocumentor::tags($fn);
$parser = new PhpdocParser($tags);

$doc = $reflection->getDocComment();
$meta = $parser->parse($doc);
```

## Current limitations

- Certain tags aren't yet supported, such as `@license` or `@category`.
- Annotations aren't supported, for example
  ```php
  /**
   * @ORM\Entity(repositoryClass="MyProject\UserRepository")
   */
  ```
- As far as I'm aware this project does not support parsing inline tags yet
- Tag `@see` is only parsed once, instead of all occurrences
- Tag `@since` ignores description
- Tag `@version` ignores description
- Tag `@var` doesn't allow for ignoring variable name
- Tag `@link` ignores description
- Tag `@deprecated` ignores description
- `CustomTag` is not extensible enough, perhaps a new implementation is needed

## Fixed from the original

The original implementation is in <https://github.com/jasny/phpdoc-parser>.

Features fixed and improved so far:

- Tags `@param` failed to handle multiline tags properly, now it's fixed
- Typo `"summery"` to `"summary"`
- Improper parsing summary in multiple lines
- Added support for `@version` tag
- `@method` interprets `static` properly
- Fixed improper parsing of unclosed or improperly closed doc block
- Previous parser failed parsing for line endings `CR` and `CRLF`, now it supports: `LF`, `CR`, `CRLF`.