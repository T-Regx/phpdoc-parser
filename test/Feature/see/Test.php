<?php
namespace Test\Feature\see;

use PHPUnit\Framework\TestCase;
use Test\Fixtures\ParseAssertion;
use function Test\Fixture\resource;

class Test extends TestCase
{
    use ParseAssertion;

    /**
     * @test
     */
    public function shouldParseSeeField()
    {
        // when, then
        $this->assertParses(resource('tags/see.field.txt'), ['see' => 'MyClass::$items']);
    }

    /**
     * @test
     */
    public function shouldParseSeeMethod()
    {
        // when, then
        $this->assertParses(resource('tags/see.method.txt'), ['see' => 'MyClass::setItems()']);
    }

    /**
     * @test
     */
    public function shouldParseSeeFunction()
    {
        // when, then
        $this->assertParses(resource('tags/see.function.txt'), ['see' => 'number_of()']);
    }

    /**
     * @test
     */
    public function shouldParseSeeUrl()
    {
        // when, then
        $this->assertParses(resource('tags/see.url.txt'), ['see' => 'https://example.com/my/bar']);
    }

    /**
     * @test
     */
    public function shouldParseSee()
    {
        $this->markTestIncomplete();
        // when, then
        $this->assertParses(resource('tags/see.many.txt'), [
            'see' => 'https://example.com/my/bar'
        ]);
    }
}
