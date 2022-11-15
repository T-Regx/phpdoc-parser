<?php
namespace Test\Feature\author;

use Jasny\PhpdocParser\PhpdocException;
use Jasny\PhpdocParser\PhpdocParser;
use Jasny\PhpdocParser\PhpDocumentor;
use PHPUnit\Framework\TestCase;
use function Test\Fixture\resource;

class Test extends TestCase
{
    /**
     * @test
     */
    public function shouldParseAuthor()
    {
        // when, then
        $this->assertParses(resource('author.name.txt'), [
            'author' => ['name' => 'My Name',]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseAuthorEmail()
    {
        // when, then
        $this->assertParses(resource('author.name.email.txt'), [
            'author' => [
                'name' => 'My Name',
                'email' => 'my.name@example.com',
            ]
        ]);
    }

    /**
     * @test
     */
    public function shouldParseAuthorEmailNoName()
    {
        // when, then
        $this->assertParses(resource('author.email.txt'), [
            'author' => [
                'name' => '',
                'email' => 'my.name@example.com',
            ]
        ]);
    }

    private function assertParses(string $phpDoc, array $expected): void
    {
        // when
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->assertSame($expected, $parser->parse($phpDoc));
    }

    /**
     * @test
     */
    public function testMalformed(): void
    {
        // given
        $parser = new PHPDocParser(PhpDocumentor::tags());
        // then
        $this->expectException(PhpdocException::class);
        $this->expectExceptionMessage("Failed to parse '@author name <opening': invalid syntax");
        // when
        $parser->parse(resource('author.malformed.txt'));
    }
}
