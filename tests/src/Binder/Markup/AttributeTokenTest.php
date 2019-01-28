<?php

namespace Binder\Markup;

use Binder\Entry;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Markup\AttributeToken
 */
class AttributeTokenTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getKey
     */
    public function testGetKey(): void
    {
        $obj = new AttributeToken("sample");
        $this->assertSame("sample", $obj->getKey());
    }

    /**
     * @param string|array $content
     * @param string $expected
     * @covers ::__construct
     * @covers ::translate
     * @covers ::<private>
     * @dataProvider provideTestTranslate
     */
    public function testTranslate($content, string $expected): void
    {
        $obj = new AttributeToken("test");
        $e   = new Entry(["test"]);
        $e->set("test", $content);
        $this->assertSame($expected, $obj->translate($e));
    }

    /**
     * @return array
     */
    public function provideTestTranslate(): array
    {
        $arg1 = [
            "name"    => "test",
            "value"   => "<sample>" . PHP_EOL . "\"hogehoge\"",
            "checked" => null,
            "disabled",
        ];
        $exp1 = ' name="test" value="&lt;sample&gt;&#xa;&quot;hogehoge&quot;" checked disabled';

        return [
            [$arg1, $exp1],
            [[], ""],
            ["", ""],
        ];
    }
}
