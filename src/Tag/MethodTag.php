<?php
namespace Jasny\PhpdocParser\Tag;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\Tag;
use function call_user_func;
use function Jasny\array_only as array_only;
use function trim;

class MethodTag implements Tag
{
    /** @var string */
    private $name;
    /** @var callable|null */
    private $fqsenConvertor;

    public function __construct(string $name, ?callable $fqsenConvertor = null)
    {
        $this->name = $name;
        $this->fqsenConvertor = $fqsenConvertor;
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

        if (isset($method['return_type']) && isset($this->fqsenConvertor)) {
            $method['return_type'] = call_user_func($this->fqsenConvertor, $method['return_type']);
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
        $rawParams = preg_split('/\s*,\s*/', $parametersString);

        $regexp = '/^(?:(?<type>[^$]+)\s+)?\$(?<name>\w+)(?:\s*=\s*(?<default>"[^"]+"|\[[^\]]+\]|[^,]+))?$/';

        foreach ($rawParams as $rawParam) {
            if (!preg_match($regexp, $rawParam, $param)) {
                throw new PhpdocException("Failed to parse '@{$this->name} {$value}': invalid syntax");
            }
            if (isset($param['type']) && $param['type'] === '') {
                unset($param['type']);
            }
            if (isset($param['type']) && isset($this->fqsenConvertor)) {
                $param['type'] = call_user_func($this->fqsenConvertor, $param['type']);
            }
            if (isset($param['default'])) {
                $param['default'] = trim($param['default'], '"\'');
            }
            $params[$param['name']] = array_only($param, ['type', 'name', 'default']);
        }

        return $params;
    }
}