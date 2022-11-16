<?php
namespace Test\Feature\tags\todo;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseTodo()
    {
        // when, then
        $this->assertParses(resource('tags/todo.txt'), [
            'todo' => 'Add an array parameter to count.'
        ]);
    }
}
