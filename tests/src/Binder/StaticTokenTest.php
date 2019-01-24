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
        $obj = new StaticToken("hogehoge");
        $this->assertSame("hogehoge", $obj->translate(new Entry([])));
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
}
