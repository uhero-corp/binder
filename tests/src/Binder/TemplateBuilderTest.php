<?php

namespace Binder;

use Binder\Markup\Comment;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\TemplateBuilder
 */
class TemplateBuilderTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::setBreakCode
     * @covers ::getBreakCode
     */
    public function testSetBreakCodeAndGetBreakCode(): void
    {
        $obj = new TemplateBuilder();
        $this->assertNull($obj->getBreakCode());
        $obj->setBreakCode("\r\n");
        $this->assertSame("\r\n", $obj->getBreakCode());
    }

    /**
     * @covers ::__construct
     * @covers ::addSymbol
     * @covers ::getSymbols
     */
    public function testAddSymbolAndGetSymbols(): void
    {
        $s1  = new Variable("{", "}");
        $s2  = new Variable(":", "");
        $s3  = new Comment("$", "");
        $obj = new TemplateBuilder();
        $obj->addSymbol($s1);
        $obj->addSymbol($s2);
        $obj->addSymbol($s3);
        $this->assertSame([$s1, $s2, $s3], $obj->getSymbols());
    }

    /**
     * @covers ::__construct
     * @covers ::build
     */
    public function testBuildFail(): void
    {
        $this->expectException(LogicException::class);
        $obj = new TemplateBuilder();
        $obj->build("This is {test} template");
    }

    /**
     * @covers ::__construct
     * @covers ::build
     */
    public function testBuild(): void
    {
        $obj = new TemplateBuilder();
        $obj->addSymbol(new Variable("%", "%"));
        $obj->setBreakCode("\r\n");

        $t   = $obj->build("Sample: {aaa}, %bbb%, {ccc}, %ddd%");
        $this->assertSame(["bbb" => null, "ddd" => null], $t->getEmptyMapping());
        $this->assertSame("\r\n", $t->getBreakCode());
    }
}
