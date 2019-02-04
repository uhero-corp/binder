<?php

namespace Binder\Markup;

use Binder\NamedToken;
use Binder\TokenExtraction;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Markup\Comment
 */
class CommentTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructFailByEmptyPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Comment("", "");
    }

    /**
     * @param string $text
     * @param string $name
     * @param string $leftPart
     * @param string $rightPart
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParse
     */
    public function testParse(string $text, string $name, string $leftPart, string $rightPart): void
    {
        $obj      = new Comment("{", "}");
        $actual   = $obj->parse($text);
        $expected = TokenExtraction::done(new NamedToken($name), $leftPart, $rightPart);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideTestParse(): array
    {
        return [
            ["<p>This is <!--{sample}--> test.</p>", "sample", "<p>This is ", " test.</p>"],
            ["<p>Whitespace is <!-- {sample} --> allowed.</p>", "sample", "<p>Whitespace is ", " allowed.</p>"],
            ["<!-- test1 --><!-- {var} --><!-- test2 -->", "var", "<!-- test1 -->", "<!-- test2 -->"],
            ["<p>First: <!--{this/is/NG}-->, Second: <!--{ok}--></p>", "ok", "<p>First: <!--{this/is/NG}-->, Second: ", "</p>"],
        ];
    }

    /**
     * @param string $text
     * @covers ::__construct
     * @covers ::parse
     * @covers ::<private>
     * @dataProvider provideTestParseReturnsFail
     */
    public function testParseReturnsFail(string $text): void
    {
        $obj = new Comment("{", "}");
        $this->assertSame(TokenExtraction::fail(), $obj->parse($text));
    }

    /**
     * @return array
     */
    public function provideTestParseReturnsFail(): array
    {
        return [
            ["This is <!-- { invalid paramter } -->"],
            ["<!-- normal comment -->"],
            ["Corruped: <!--{hogehoge"],
            ["Invalid suffix: <!-- {hogehoge]-->"],
            ["Invalid comment end: <!-- {hogehoge} !>"],
        ];
    }
}
