<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;

class AuthorTag implements Tag
{
    /** @var string */
    private $name;
    /** @var string */
    private $regexp;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->regexp = '/^(?<name>[^<>]*?)\s*(?:<(?<email>\S*)>)?$/';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $notations[$this->name] = $this->processed($value);
        return $notations;
    }

    private function processed(string $tagSourceCode): array
    {
        if (!preg_match($this->regexp, $tagSourceCode, $match)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $tagSourceCode': invalid syntax");
        }
        $author = ['name' => $match['name']];
        if (\array_key_exists('email', $match)) {
            $author['email'] = $match['email'];
        }
        return $author;
    }
}
