<?php

namespace Binder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Entry
 */
class EntryTest extends TestCase
{
    /**
     * @return Entry
     */
    private function getTestObject(): Entry
    {
        return Template::read("{name}, {age}, {gender}")->entry();
    }

    /**
     * set() でパラメータを指定し、get() でそれを取り出すことができます。
     *
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     * @covers ::<private>
     */
    public function testSetAndGet(): void
    {
        $obj = $this->getTestObject();
        $obj->set("name", "John");
        $obj->set("age", 18);
        $obj->set("gender", "M");
        $this->assertSame("John", $obj->get("name"));
        $this->assertSame(18, $obj->get("age"));
        $this->assertSame("M", $obj->get("gender"));
    }

    /**
     * set() はこのオブジェクト自身を返します。
     *
     * @covers ::__construct
     * @covers ::set
     * @covers ::<private>
     */
    public function testSetReturnsThis(): void
    {
        $obj1 = $this->getTestObject();
        $obj2 = $obj1->set("name", "John");
        $this->assertSame($obj1, $obj2);
    }

    /**
     * まだ set() で代入されていないパラメータを get() で取り出した場合は null を返します。
     *
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testGet(): void
    {
        $obj = $this->getTestObject();
        $this->assertNull($obj->get("name"));
    }

    /**
     * 存在しないキーを指定して set() を実行した場合 InvalidArgumentException をスローします。
     *
     * @covers ::__construct
     * @covers ::set
     * @covers ::<private>
     */
    public function testSetFailWithInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = $this->getTestObject();
        $obj->set("id", 1);
    }

    /**
     * 存在しないキーを指定して get() を実行した場合 InvalidArgumentException をスローします。
     *
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testGetFailWithInvalidKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = $this->getTestObject();
        $obj->get("id");
    }

    /**
     * keys() は、コンストラクタ引数で指定した Template に含まれるキーの一覧を返します。
     *
     * @covers ::__construct
     * @covers ::keys
     */
    public function testKeys(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame(["name", "age", "gender"], $obj->keys());
    }
}
