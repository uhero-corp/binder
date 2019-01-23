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
     * set() でパラメータを指定し、get() でそれを取り出すことができます。
     *
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     * @covers ::<private>
     */
    public function testSetAndGet(): void
    {
        $obj = new Entry(["name", "age", "gender"]);
        $obj->set("name", "John");
        $obj->set("age", 18);
        $obj->set("gender", "M");
        $this->assertSame("John", $obj->get("name"));
        $this->assertSame(18, $obj->get("age"));
        $this->assertSame("M", $obj->get("gender"));
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
        $obj = new Entry(["name", "age", "gender"]);
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
        $obj = new Entry(["name", "age", "gender"]);
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
        $obj = new Entry(["name", "age", "gender"]);
        $obj->get("id");
    }

    /**
     * keys() は、コンストラクタ引数で指定したキーの一覧を返します。
     *
     * @covers ::__construct
     * @covers ::keys
     */
    public function testKeys(): void
    {
        $obj = new Entry(["name", "age", "gender"]);
        $this->assertSame(["name", "age", "gender"], $obj->keys());
    }
}
