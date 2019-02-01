<?php

namespace Binder;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\BlockLine
 */
class BlockLineTest extends TestCase
{
    /**
     * @param string|array $value
     * @param array $expected
     * @covers ::__construct
     * @covers ::translate
     * @covers ::<private>
     * @dataProvider provideTestTranslate
     */
    public function testTranslate($value, array $expected): void
    {
        $obj = new BlockLine("text", "    ");
        $e   = new Entry(Template::read("{text}"));
        $e->set("text", $value);
        $this->assertSame($expected, $obj->translate($e));
    }

    /**
     * @return array
     */
    public function provideTestTranslate(): array
    {
        return [
            $this->getTranslateEmptyCase(),
            $this->getTranslateOneLineCase(),
            $this->getTranslateMultiLineCase(),
            $this->getTranslateTrimmedCase(),
            $this->getTranslateArrayCase(),
        ];
    }

    /**
     * @return array
     */
    private function getTranslateEmptyCase(): array
    {
        return ["", []];
    }

    /**
     * @return array
     */
    private function getTranslateOneLineCase(): array
    {
        return ["This is test", ["    This is test"]];
    }

    /**
     * @return array
     */
    private function getTranslateMultiLineCase(): array
    {
        $text = implode(PHP_EOL, [
            "<ul>",
            "    <li>first</li>",
            "    <li>second</li>",
            "    <li>third</li>",
            "</ul>",
        ]);

        $expected = [
            "    <ul>",
            "        <li>first</li>",
            "        <li>second</li>",
            "        <li>third</li>",
            "    </ul>",
        ];
        return [$text, $expected];
    }

    /**
     * @return array
     */
    private function getTranslateTrimmedCase(): array
    {
        $text = implode(PHP_EOL, [
            "",
            "This is a pen.",
            "This is an apple.",
            "Hello world.",
            "",
        ]);

        $expected = [
            "    This is a pen.",
            "    This is an apple.",
            "    Hello world.",
        ];
        return [$text, $expected];
    }

    /**
     * @return array
     */
    private function getTranslateArrayCase(): array
    {
        $arr = [
            "This is a pen.",
            "This is an apple.",
            "Hello world.",
        ];

        $expected = [
            "    This is a pen.",
            "    This is an apple.",
            "    Hello world.",
        ];
        return [$arr, $expected];
    }

    /**
     * @covers ::__construct
     * @covers ::getKeys
     */
    public function testGetKeys(): void
    {
        $obj = new BlockLine("text", "    ");
        $this->assertSame(["text"], $obj->getKeys());
    }
}
