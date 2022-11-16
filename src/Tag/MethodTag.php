<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use TRegx\CleanRegex\Pattern;

class MethodTag implements Tag
{
    /** @var string */
    private $name;
    /** @var ClassName */
    private $className;
    /** @var Pattern */
    private $methodPattern;

    public function __construct(string $name, ClassName $className)
    {
        $this->name = $name;
        $this->className = $className;
        $this->methodPattern = Pattern::of('^
          \s*
          (static\s+)?
          (?:(?<return_type>\S+)\s+)?
          (?<name>\w+)
          \(
          (?<params>[^\)]+)?
          \)
          (?:\s+(?<description>.*))?', 'x');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $matcher = $this->methodPattern->match($value);
        if ($matcher->fails()) {
            throw new PhpdocException("Failed to parse '@$this->name $value': invalid syntax");
        }

        $methodTag = $matcher->first();
        $method = ['name' => $methodTag->get('name')];
        if ($methodTag->matched('return_type')) {
            $method['return_type'] = $this->className->apply($methodTag->get('return_type'));
        }
        if ($methodTag->matched('params')) {
            $method['params'] = $this->processParams($value, $methodTag->get('params'));
        } else {
            $method['params'] = [];
        }

        if ($methodTag->matched('description')) {
            $method['description'] = $methodTag->get('description');
        }

        $notations[$this->name] = $method;
        return $notations;
    }

    private function processParams(string $value, string $parametersString): array
    {
        $params = [];
        $rawParams = Pattern::of('\s*,\s*')->split($parametersString);
        $methodPattern = Pattern::of('^(?:(?<type>[^$]+)\s+)?\$(?<name>\w+)(?:\s*=\s*(?<default>"[^"]+"|\[[^\]]+\]|[^,]+))?$');

        foreach ($rawParams as $rawParam) {
            $matcher = $methodPattern->match($rawParam);
            if ($matcher->test()) {
                $argument = $matcher->first();

                $methodArgument = ['name' => $argument->get('name')];
                if ($argument->matched('type') && $argument->get('type') !== '') {
                    $methodArgument['type'] = $this->className->apply($argument->get('type'));
                }
                if ($argument->matched('default')) {
                    $methodArgument['default'] = \trim($argument->get('default'), '"\'');
                }
                $params[$argument->get('name')] = $methodArgument;
            } else {
                throw new PhpdocException("Failed to parse '@$this->name $value': invalid syntax");
            }
        }
        return $params;
    }
}
