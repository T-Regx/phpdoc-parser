<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;

class AuthorTag implements Tag
{
    /** @var string */
    private $name;
    /** @var Pattern */
    private $authorPattern;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->authorPattern = Pattern::of('^(?<name>[^<>]*?)\s*(?:<(?<email>\S*)>)?$');
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
        $matcher = $this->authorPattern->match($tagSourceCode);
        if ($matcher->test()) {
            $match = $matcher->first();
            $author = ['name' => $match->get('name')];
            if ($match->matched('email')) {
                $author['email'] = $match->get('email');
            }
            return $author;
        }
        throw new PhpdocException("Failed to parse '@$this->name $tagSourceCode': invalid syntax");
    }
}
