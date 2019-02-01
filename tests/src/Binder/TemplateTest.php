<?php

namespace Binder;

use Binder\Markup\Comment;
use Binder\Markup\DataAttribute;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Binder\Template
 */
class TemplateTest extends TestCase
{
    /**
     * @return string
     */
    private function getSampleText(): string
    {
        return implode(PHP_EOL, [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>{title}</title>',
            '    </head>',
            '    <body>',
            '        <h1>{title}</h1>',
            '        ',
            '        <p>I am {name}, {age} years old.</p>',
            '        {contents}',
            '    </body>',
            '</html>',
        ]);
    }

    /**
     * @return TemplateBuilder
     */
    private function getSampleBuilder(): TemplateBuilder
    {
        $builder = new TemplateBuilder();
        $builder->addSymbol(new Variable("{", "}"));
        $builder->setBreakCode("\n");
        return $builder;
    }

    /**
     * @return Template
     */
    private function getSampleObject(): Template
    {
        return $this->getSampleBuilder()->build($this->getSampleText());
    }

    /**
     * @covers ::newInstance
     * @covers ::getLines
     * @covers ::<private>
     */
    public function testGetLines(): void
    {
        $expected = [
            new StaticLine('<!DOCTYPE html>'),
            new StaticLine('<html lang="ja">'),
            new StaticLine('    <head>'),
            new StaticLine('        <meta charset="UTF-8">'),
            new MixedLine([
                new StaticToken('        '),
                new StaticToken('<title>'),
                new NamedToken('title'),
                new StaticToken('</title>'),
                ]),
            new StaticLine('    </head>'),
            new StaticLine('    <body>'),
            new MixedLine([
                new StaticToken('        '),
                new StaticToken('<h1>'),
                new NamedToken('title'),
                new StaticToken('</h1>'),
                ]),
            new StaticLine('        '),
            new MixedLine([
                new StaticToken('        '),
                new StaticToken('<p>I am '),
                new NamedToken('name'),
                new StaticToken(', '),
                new NamedToken('age'),
                new StaticToken(' years old.</p>'),
                ]),
            new BlockLine("contents", "        "),
            new StaticLine('    </body>'),
            new StaticLine('</html>'),
        ];

        $obj = $this->getSampleObject();
        $this->assertEquals($expected, $obj->getLines());
    }

    /**
     * @covers ::newInstance
     * @covers ::getEmptyMapping
     * @covers ::<private>
     */
    public function testGetEmptyMapping(): void
    {
        $expected = ["title" => null, "name" => null, "age" => null, "contents" => null];
        $obj      = $this->getSampleObject();
        $this->assertSame($expected, $obj->getEmptyMapping());
    }

    /**
     * @covers ::newInstance
     * @covers ::getBreakCode
     * @covers ::<private>
     */
    public function testGetBreakCode(): void
    {
        $obj = $this->getSampleObject();
        $this->assertSame("\n", $obj->getBreakCode());
    }

    /**
     * @covers ::createDefaultBuilder
     */
    public function testCreateDefaultBuilder(): void
    {
        $builder = new TemplateBuilder();
        $builder->addSymbol(new Variable("{", "}"));
        $this->assertEquals($builder, Template::createDefaultBuilder());
    }

    /**
     * @covers ::createDefaultMarkupBuilder
     */
    public function testCreateDefaultMarkupBuilder(): void
    {
        $builder = new TemplateBuilder();
        $builder->addSymbol(new Comment("{", "}"));
        $builder->addSymbol(new DataAttribute("bind"));
        $builder->addSymbol(new Variable("{", "}"));
        $builder->setBreakCode("\n");
        $this->assertEquals($builder, Template::createDefaultMarkupBuilder());
    }

    /**
     * @covers ::read
     * @covers ::newInstance
     */
    public function testRead(): void
    {
        $text = $this->getSampleText();
        $obj1 = Template::read($text);
        $obj2 = $this->getSampleBuilder()->build($text);
        $this->assertEquals($obj1, $obj2);
    }

    /**
     * @covers ::readMarkup
     * @covers ::newInstance
     */
    public function testReadMarkup(): void
    {
        $text = implode("\n", [
            '<!DOCTYPE html>',
            '<html lang="ja">',
            '    <head>',
            '        <meta charset="UTF-8">',
            '        <title>{title}</title>',
            '        <!--{css_list}-->',        // no whitespace in the comment
            '    </head>',
            '    <body>',
            '        <h1>{title}</h1>',
            '        <ul data-bind="ul_attr">',
            '            <!--  {li_list}  -->', // whitespaces in the comment
            '        </ul>',
            '    </body>',
            '</html>',
        ]);

        $expected = [
            new StaticLine('<!DOCTYPE html>'),
            new StaticLine('<html lang="ja">'),
            new StaticLine('    <head>'),
            new StaticLine('        <meta charset="UTF-8">'),
            new MixedLine([
                new StaticToken("        "),
                new StaticToken('<title>'),
                new NamedToken("title"),
                new StaticToken('</title>'),
            ]),
            new BlockLine("css_list", "        "),
            new StaticLine('    </head>'),
            new StaticLine('    <body>'),
            new MixedLine([
                new StaticToken("        "),
                new StaticToken('<h1>'),
                new NamedToken('title'),
                new StaticToken('</h1>'),
            ]),
            new MixedLine([
                new StaticToken("        "),
                new StaticToken('<ul'),
                new Markup\AttributeToken("ul_attr"),
                new StaticToken('>'),
            ]),
            new BlockLine("li_list", "            "),
            new StaticLine('        </ul>'),
            new StaticLine('    </body>'),
            new StaticLine('</html>'),
        ];

        $mapping = [
            "title"    => null,
            "css_list" => null,
            "ul_attr"  => null,
            "li_list"  => null,
        ];
        $obj = Template::readMarkup($text);
        $this->assertEquals($expected, $obj->getLines());
        $this->assertSame($mapping, $obj->getEmptyMapping());
        $this->assertSame("\n", $obj->getBreakCode());
    }

    /**
     * @covers ::entry
     * @covers ::newInstance
     */
    public function testEntry()
    {
        $obj = $this->getSampleObject();
        $e   = $obj->entry();
        $this->assertSame(["title", "name", "age", "contents"], $e->keys());
    }
}
