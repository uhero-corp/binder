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
        $f = function () {
            return "Male";
        };

        $obj = $this->getTestObject();
        $obj->set("name", "John");
        $obj->set("age", 18);
        $obj->set("gender", $f);
        $this->assertSame("John", $obj->get("name"));
        $this->assertSame(18, $obj->get("age"));
        $this->assertSame($f, $obj->get("gender"));
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

    /**
     * @covers ::__construct
     * @covers ::render
     */
    public function testRenderByDefault(): void
    {
        $text = implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>{title}</title>',
            '    </head>',
            '    <body>',
            '        <h1>{title}</h1>',
            '        <p>I am {name}, {age} years old.</p>',
            '        {contents}',
            '    </body>',
            '</html>',
        ]);
        $contents  = implode(PHP_EOL, [
            '<h2>My favorite foods</h2>',
            '<ul>',
            '    <li>Steak</li>',
            '    <li>Donuts</li>',
            '    <li>Pizza</li>',
            '</ul>',
        ]);
        $expected1 = implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>Sample Document</title>',
            '    </head>',
            '    <body>',
            '        <h1>Sample Document</h1>',
            '        <p>I am John, 18 years old.</p>',
            '    </body>',
            '</html>',
        ]);
        $expected2 = implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>Sample Document</title>',
            '    </head>',
            '    <body>',
            '        <h1>Sample Document</h1>',
            '        <p>I am John, 18 years old.</p>',
            '        <h2>My favorite foods</h2>',
            '        <ul>',
            '            <li>Steak</li>',
            '            <li>Donuts</li>',
            '            <li>Pizza</li>',
            '        </ul>',
            '    </body>',
            '</html>',
        ]);

        $obj = Template::read($text)->entry()
            ->set("title", "Sample Document")
            ->set("name", "John")
            ->set("age", 18);
        $this->assertSame($expected1, $obj->render());
        $obj->set("contents", $contents);
        $this->assertSame($expected2, $obj->render());
    }

    /**
     *
     * @covers ::__construct
     * @covers ::render
     */
    public function testRenderByMarkup(): void
    {
        $text = implode("\n", [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>{title}</title>',
            '        <!--{css_list}-->',
            '    </head>',
            '    <body>',
            '        <h1>{title}</h1>',
            '        <ul data-bind="ul_attr">',
            '            <!--{li_list}-->',
            '        </ul>',
            '    </body>',
            '</html>',
        ]);
        $expected = implode("\n", [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>Sample Markup</title>',
            '        <link rel="stylesheet" href="common.css">',
            '        <link rel="stylesheet" href="extra.css">',
            '    </head>',
            '    <body>',
            '        <h1>Sample Markup</h1>',
            '        <ul id="list1" class="sample">',
            '            <li>Apple</li>',
            '            <li>Banana</li>',
            '            <li>Orange</li>',
            '        </ul>',
            '    </body>',
            '</html>',
        ]);
        $css = '<link rel="stylesheet" href="common.css">' . PHP_EOL . '<link rel="stylesheet" href="extra.css">';
        $li  = [
            "<li>Apple</li>",
            "<li>Banana</li>",
            "<li>Orange</li>",
        ];

        $result = Template::readMarkup($text)->entry()
            ->set("title", "Sample Markup")
            ->set("css_list", $css)
            ->set("ul_attr", ["id" => "list1", "class" => "sample"])
            ->set("li_list", $li)
            ->render();
        $this->assertSame($expected, $result);
    }
}
