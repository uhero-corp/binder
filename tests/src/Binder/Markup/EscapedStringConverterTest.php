<?php

namespace Binder\Markup;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Markup\EscapedStringConverter
 */
class EscapedStringConverterTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = EscapedStringConverter::getInstance();
        $obj2 = EscapedStringConverter::getInstance();
        $this->assertInstanceOf(EscapedStringConverter::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $obj      = EscapedStringConverter::getInstance();
        $str      = "<p>Say \"Hello World\".</p>";
        $expected = "&lt;p&gt;Say &quot;Hello World&quot;.&lt;/p&gt;";
        $this->assertSame($expected, $obj->convert($str));
    }
}
