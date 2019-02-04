<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\MixedLine
 */
class MixedLineTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::translate
     */
    public function testTranslate(): void
    {
        $t  = Template::read("{name}, {age}");
        $e1 = $t->entry();
        $e1->set("name", "Tom");
        $e1->set("age", 18);
        $e2 = $t->entry();

        $obj = new MixedLine([
            new StaticToken("My name is "),
            new NamedToken("name"),
            new StaticToken(". I am "),
            new NamedToken("age"),
            new StaticToken(" years old."),
        ]);

        $this->assertSame(["My name is Tom. I am 18 years old."], $obj->translate($e1));
        $this->assertSame(["My name is . I am  years old."], $obj->translate($e2));
    }

    /**
     * @covers ::__construct
     * @covers ::getKeys
     */
    public function testGetKeys(): void
    {
        $obj = new MixedLine([
            new StaticToken("My name is "),
            new NamedToken("name"),
            new StaticToken(". I am "),
            new NamedToken("age"),
            new StaticToken(" years old."),
        ]);
        $this->assertSame(["name", "age"], $obj->getKeys());
    }
}
