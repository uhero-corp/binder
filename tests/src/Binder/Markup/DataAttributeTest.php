<?php

namespace Binder\Markup;

use Binder\TokenExtraction;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Markup\DataAttribute
 */
class DataAttributeTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructFailByInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DataAttribute("this/is/invalid");
    }

    /**
     * @covers ::__construct
     */
    public function testConstructByDefault(): void
    {
        $obj1 = new DataAttribute();
        $obj2 = new DataAttribute("bind");
        $this->assertEquals($obj1, $obj2);
    }

    /**
     * @param string $text
     * @param string $name
     * @param string $leftPart
     * @param string $rightPart
     * @covers ::__construct
     * @covers ::parse
     * @dataProvider provideTestParse
     */
    public function testParse(string $text, string $name, string $leftPart, string $rightPart): void
    {
        $obj      = new DataAttribute();
        $actual   = $obj->parse($text);
        $expected = TokenExtraction::done(new AttributeToken($name), $leftPart, $rightPart);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function provideTestParse(): array
    {
        return [
            ['<img data-bind="test1"><img data-bind="test2">', "test1", "<img", '><img data-bind="test2">'],
            ['<img data-bind="this/is/NG"><img data-bind="test2">', "test2", '<img data-bind="this/is/NG"><img', ">"],
            [' data-bind="test"', "test", "", ""],
        ];
    }

    /**
     * @param string $text
     * @covers ::__construct
     * @covers ::parse
     * @dataProvider provideTestParseReturnsFail
     */
    public function testParseReturnsFail(string $text): void
    {
        $obj = new DataAttribute();
        $this->assertSame(TokenExtraction::fail(), $obj->parse($text));
    }

    /**
     * @return array
     */
    public function provideTestParseReturnsFail(): array
    {
        return [
            ['<div id="test" data-notbind="test"></div>'],
            ['<div id="test" data-bind="this/is/NG"></div>'],
            ['<div id="test" data-bind=""></div>'],
            ['data-bind="hoge"'],
        ];
    }
}
