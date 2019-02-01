<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\StaticLine
 */
class StaticLineTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::translate
     */
    public function testTranslate(): void
    {
        $e   = new Entry(Template::read("test: {hoge}"));
        $obj = new StaticLine("piyopiyo");
        $this->assertSame(["piyopiyo"], $obj->translate($e));
    }

    /**
     * @covers ::__construct
     * @covers ::getKeys
     */
    public function testGetKeys(): void
    {
        $obj = new StaticLine("hogehoge");
        $this->assertSame([], $obj->getKeys());
    }
}
