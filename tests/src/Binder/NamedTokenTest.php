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
        $e    = new Entry(["name", "age", "gender"]);
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
        $e   = new Entry(["hoge"]);
        $e->set("hoge", ["This", "is", "a", "pen"]);
        $this->assertSame("This is a pen", $obj->translate($e));
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
     * @covers ::__construct
     * @covers ::createLine
     */
    public function testCreateLine(): void
    {
        $obj      = new NamedToken("test");
        $expected = new BlockLine("test", "  ");
        $this->assertEquals($expected, $obj->createLine("  "));
    }
}
