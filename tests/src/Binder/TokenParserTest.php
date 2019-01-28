<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\TokenParser
 */
class TokenParserTest extends TestCase
{
    /**
     * @param Variable $v
     * @param string $text
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParseBySingleSymbol
     */
    public function testParseBySingleSymbol(Variable $v, string $text): void
    {
        $expected = [
            new StaticToken("I am "),
            new NamedToken("name"),
            new StaticToken(", "),
            new NamedToken("age"),
            new StaticToken(" years old."),
        ];

        $obj = new TokenParser([$v]);
        $this->assertEquals($expected, $obj->parse($text));
    }

    /**
     * @return array
     */
    public function provideTestParseBySingleSymbol(): array
    {
        $s1 = new Variable(":", "");
        $s2 = new Variable("{", "}");

        return [
            [$s1, "I am :name, :age years old."],
            [$s2, "I am {name}, {age} years old."],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     */
    public function testParseByMultipleSymbols(): void
    {
        $expected = [
            new NamedToken("first"),
            new StaticToken(" I am "),
            new NamedToken("name"),
            new StaticToken(", "),
            new NamedToken("age"),
            new StaticToken(" years old. "),
            new NamedToken("last"),
        ];
        $symbols  = [
            new Variable(":", ""),
            new Variable("{", "}"),
        ];

        $obj    = new TokenParser($symbols);
        $result = $obj->parse(":first I am {name}, :age years old. {last}");
        $this->assertEquals($expected, $result);
    }
}
