<?php

namespace Binder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Variable
 */
class VariableTest extends TestCase
{
    /**
     * シンボルの先頭が空文字列の場合は InvalidArgumentException をスローします。
     *
     * @covers ::__construct
     */
    public function testConstractFailByEmptyPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Variable("", "}");
    }

    /**
     * @param string $prefix
     * @param string $suffix
     * @param string $text
     * @param string $name
     * @param string $leftPart
     * @param string $rightPart
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParse
     */
    public function testParse(string $prefix, string $suffix, string $text, string $name, string $leftPart, string $rightPart): void
    {
        $variable = new Variable($prefix, $suffix);
        $actual   = $variable->parse($text);
        $expected = TokenExtraction::done(new NamedToken($name), $leftPart, $rightPart);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideTestParse(): array
    {
        return [
            [":", "", "My name is :name.", "name", "My name is ", "."],
            [":", "", "I am :name, :age years old.", "name", "I am ", ", :age years old."],
            [":", "", ":varonly", "varonly", "", ""],
            ["{", "}", "I am {age} years old.", "age", "I am ", " years old."],
            ["{", "}", "The product '{name}' is {price} yen.", "name", "The product '", "' is {price} yen."],
            ["{", "}", "{varonly}", "varonly", "", ""],
        ];
    }

    /**
     * @param string $prefix
     * @param string $suffix
     * @param string $text
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParseReturnsFail
     */
    public function testParseReturnsFail(string $prefix, string $suffix, string $text): void
    {
        $variable = new Variable($prefix, $suffix);
        $this->assertSame(TokenExtraction::fail(), $variable->parse($text));
    }

    /**
     * @return array
     */
    public function provideTestParseReturnsFail(): array
    {
        return [
            [":", "", "plain text"],
            [":", "", "prefix : only"],
            ["{", "}", "Invalid suffix sample {hoge "],
            ["{", "}", "Invalid parameter name sample {this/is/NG}"],
        ];
    }

    /**
     * @param string $prefix
     * @param string $suffix
     * @param string $text
     * @param string $name
     * @param string $leftPart
     * @param string $rightPart
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParseReturnsSecondVar
     */
    public function testParseReturnsSecondVar(string $prefix, string $suffix, string $text, string $name, string $leftPart, string $rightPart): void
    {
        $variable = new Variable($prefix, $suffix);
        $actual   = $variable->parse($text);
        $expected = TokenExtraction::done(new NamedToken($name), $leftPart, $rightPart);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideTestParseReturnsSecondVar(): array
    {
        return [
            [":", "", "Test : target :second is here", "second", "Test : target ", " is here"],
            ["{", "}", "Test {this/is/NG} but {this_one} is valid", "this_one", "Test {this/is/NG} but ", " is valid"],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getTokenFactory
     */
    public function testGetTokenFactory(): void
    {
        $obj1 = new Variable("{", "}");
        $this->assertNull($obj1->getTokenFactory());
        $tf   = $this->createTestTokenFactory();
        $obj2 = new Variable("{", "}", $tf);
        $this->assertSame($tf, $obj2->getTokenFactory());
    }

    /**
     * @return TokenFactory
     */
    private function createTestTokenFactory(): TokenFactory
    {
        $c = new class implements StringConverter {
            public function convert($str): string
            {
                return "({$str})";
            }
        };
        return new NamedTokenFactory($c);
    }

    /**
     * @covers ::parse
     */
    public function testParseByCustomTokenFactory(): void
    {
        $tf       = $this->createTestTokenFactory();
        $obj      = new Variable("{", "}", $tf);
        $expected = TokenExtraction::done($tf->create("test"), "this is ", " data");
        $this->assertEquals($expected, $obj->parse("this is {test} data"));
    }
}
