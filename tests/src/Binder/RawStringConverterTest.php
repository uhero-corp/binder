<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\RawStringConverter
 */
class RawStringConverterTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = RawStringConverter::getInstance();
        $obj2 = RawStringConverter::getInstance();
        $this->assertInstanceOf(RawStringConverter::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $obj = RawStringConverter::getInstance();
        $str = "<p>It's test</p>";
        $this->assertSame($str, $obj->convert($str));
    }
}
