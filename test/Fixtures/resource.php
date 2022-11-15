<?php
namespace Test\Fixture;

function resource(string $resourceName): string
{
    $path = __DIR__ . '/../resources/' . $resourceName;
    if (\file_exists($path)) {
        return \file_get_contents($path);
    }
    throw new \RuntimeException("Resource '$resourceName' not found");
}
