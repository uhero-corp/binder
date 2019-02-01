<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\StaticToken
 */
class StaticTokenTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::translate
     */
    public function testTranslate(): void
    {
        $e   = new Entry(Template::read("test: {hoge}"));
        $obj = new StaticToken("hogehoge");
        $this->assertSame("hogehoge", $obj->translate($e));
    }

    /**
     * @covers ::__construct
     * @covers ::getKey
     */
    public function testGetKey(): void
    {
        $obj = new StaticToken("hogehoge");
        $this->assertSame("", $obj->getKey());
    }

    /**
     * @covers ::__construct
     * @covers ::createLine
     */
    public function testCreateLine(): void
    {
        $obj      = new StaticToken("hogehoge");
        $expected = new StaticLine("\t\thogehoge");
        $this->assertEquals($expected, $obj->createLine("\t\t"));
    }
}
