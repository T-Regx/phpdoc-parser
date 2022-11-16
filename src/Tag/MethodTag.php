<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\ClassName\ClassName;
use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function Jasny\array_only;
use function trim;

class MethodTag implements Tag
{
    /** @var string */
    private $name;
    /** @var ClassName */
    private $className;

    public function __construct(string $name, ClassName $className)
    {
        $this->name = $name;
        $this->className = $className;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function process(array $notations, string $value): array
    {
        $regexp = '/^(?:(?<return_type>\S+)\s+)?(?<name>\w+)\((?<params>[^\)]+)?\)(?:\s+(?<description>.*))?/';

        if (!preg_match($regexp, $value, $method)) {
            throw new PhpdocException("Failed to parse '@{$this->name} $value': invalid syntax");
        }

        if (isset($method['return_type'])) {
            $method['return_type'] = $this->className->apply($method['return_type']);
        }

        if (isset($method['params'])) {
            $method['params'] = $this->processParams($value, $method['params']);
        } else {
            $method['params'] = [];
        }
        $method = array_only($method, ['return_type', 'name', 'params', 'description']);

        $notations[$this->name] = $method;
        return $notations;
    }

    private function processParams(string $value, string $parametersString): array
    {
        $params = [];
        $rawParams = \preg_split('/\s*,\s*/', $parametersString);

        $regexp = '/^(?:(?<type>[^$]+)\s+)?\$(?<name>\w+)(?:\s*=\s*(?<default>"[^"]+"|\[[^\]]+\]|[^,]+))?$/';

        foreach ($rawParams as $rawParam) {
            if (!preg_match($regexp, $rawParam, $param)) {
                throw new PhpdocException("Failed to parse '@{$this->name} {$value}': invalid syntax");
            }
            if (isset($param['type']) && $param['type'] === '') {
                unset($param['type']);
            }
            if (isset($param['type'])) {
                $param['type'] = $this->className->apply($param['type']);
            }
            if (isset($param['default'])) {
                $param['default'] = trim($param['default'], '"\'');
            }
            $params[$param['name']] = array_only($param, ['type', 'name', 'default']);
        }

        return $params;
    }
}
