<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\TokenExtraction
 */
class TokenExtractionTest extends TestCase
{
    /**
     * @return TokenExtraction
     */
    private function createDoneObject(): TokenExtraction
    {
        return TokenExtraction::done(new NamedToken("test"), "left ", " right");
    }

    /**
     * @covers ::fail
     * @covers ::<private>
     */
    public function testFailReturnsSameObject()
    {
        $obj1 = TokenExtraction::fail();
        $obj2 = TokenExtraction::fail();
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @covers ::done
     * @covers ::fail
     * @covers ::getLeftPart
     * @covers ::<private>
     */
    public function testGetLeftPart(): void
    {
        $obj1 = $this->createDoneObject();
        $obj2 = TokenExtraction::fail();
        $this->assertSame("left ", $obj1->getLeftPart());
        $this->assertNull($obj2->getLeftPart());
    }

    /**
     * @covers ::done
     * @covers ::fail
     * @covers ::getRightPart
     * @covers ::<private>
     */
    public function testGetRightPart(): void
    {
        $obj1 = $this->createDoneObject();
        $obj2 = TokenExtraction::fail();
        $this->assertSame(" right", $obj1->getRightPart());
        $this->assertNull($obj2->getRightPart());
    }

    /**
     * @covers ::done
     * @covers ::fail
     * @covers ::getToken
     * @covers ::<private>
     */
    public function testGetToken(): void
    {
        $expected = new NamedToken("test");
        $obj1     = $this->createDoneObject();
        $obj2     = TokenExtraction::fail();
        $this->assertEquals($expected, $obj1->getToken());
        $this->assertNull($obj2->getToken());
    }

    /**
     * @covers ::done
     * @covers ::fail
     * @covers ::isFailure
     * @covers ::<private>
     */
    public function testIsFailure(): void
    {
        $obj1 = $this->createDoneObject();
        $obj2 = TokenExtraction::fail();
        $this->assertFalse($obj1->isFailure());
        $this->assertTrue($obj2->isFailure());
    }
}
