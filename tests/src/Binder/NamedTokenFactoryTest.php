<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\NamedTokenFactory
 */
class NamedTokenFactoryTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreate()
    {
        $c  = new class implements StringConverter {
            public function convert($str): string
            {
                return "_{$str}_";
            }
        };
        $t1 = new NamedToken("test");
        $o1 = new NamedTokenFactory();
        $t2 = new NamedToken("hoge", $c);
        $o2 = new NamedTokenFactory($c);
        $this->assertEquals($t1, $o1->create("test"));
        $this->assertEquals($t2, $o2->create("hoge"));
    }
}
