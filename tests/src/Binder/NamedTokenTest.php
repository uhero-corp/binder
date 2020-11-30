<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\NamedToken
 */
class NamedTokenTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::translate
     * @covers ::<private>
     */
    public function testTranslate(): void
    {
        $obj1 = new NamedToken("name");
        $obj2 = new NamedToken("age");
        $e    = Template::read("{name}, {age}, {gender}")->entry();
        $e->set("name", "John");
        $e->set("age", 18);
        $this->assertSame("John", $obj1->translate($e));
        $this->assertSame("18", $obj2->translate($e));
    }

    /**
     * @covers ::__construct
     * @covers ::translate
     * @covers ::<private>
     */
    public function testTranslateArray(): void
    {
        $obj = new NamedToken("hoge");
        $e   = Template::read("test: {hoge}")->entry();
        $e->set("hoge", ["This", "is", "a", "pen"]);
        $this->assertSame("This is a pen", $obj->translate($e));
    }

    /**
     * @covers ::__construct
     * @covers ::translate
     * @covers ::<private>
     */
    public function testTranslateCallable(): void
    {
        $func1 = function (string $key) {
            return str_repeat($key, 2);
        };
        $func2 = function () {
            return 8 * 4;
        };

        $obj = new NamedToken("asdf");
        $e   = Template::read("testing {asdf}")->entry();
        $e->set("asdf", [$func1, "time", $func2]);
        $this->assertSame("asdfasdf time 32", $obj->translate($e));
    }

    /**
     * @covers ::__construct
     * @covers ::getKey
     */
    public function testGetKey(): void
    {
        $obj = new NamedToken("hoge");
        $this->assertSame("hoge", $obj->getKey());
    }

    /**
     * @param NamedToken $obj
     * @param Line $expected
     * @covers ::__construct
     * @covers ::createLine
     * @dataProvider provideTestCreateLine
     */
    public function testCreateLine(NamedToken $obj, Line $expected): void
    {
        $this->assertEquals($expected, $obj->createLine("  "));
    }

    /**
     * @return array
     */
    public function provideTestCreateLine(): array
    {
        $c = $this->createTestStringConverter();
        return [
            [new NamedToken("test"), new BlockLine("test", "  ")],
            [new NamedToken("hoge", $c), new BlockLine("hoge", "  ", $c)],
        ];
    }

    /**
     * @covers ::getStringConverter
     */
    public function testGetStringConverter(): void
    {
        $obj1 = new NamedToken("test");
        $this->assertSame(RawStringConverter::getInstance(), $obj1->getStringConverter());

        $c    = $this->createTestStringConverter();
        $obj2 = new NamedToken("test", $c);
        $this->assertSame($c, $obj2->getStringConverter());
    }

    /**
     * @return StringConverter
     */
    private function createTestStringConverter(): StringConverter
    {
        return new class implements StringConverter {
            public function convert($str): string
            {
                return "({$str})";
            }
        };
    }

    /**
     * @param mixed $value
     * @param string $expected
     * @covers ::__construct
     * @covers ::translate
     * @dataProvider provideTestTranslateByCustomStringConverter
     */
    public function testTranslateByCustomStringConverter($value, string $expected): void
    {
        $obj = new NamedToken("test", $this->createTestStringConverter());
        $e   = Template::read("{test}")->entry()->set("test", $value);
        $this->assertSame($expected, $obj->translate($e));
    }

    /**
     * @return array
     */
    public function provideTestTranslateByCustomStringConverter(): array
    {
        return [
            ["this is test", "(this is test)"],
            [["this", "is", "test"], "(this) (is) (test)"],
        ];
    }
}
