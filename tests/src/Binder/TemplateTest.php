<?php

namespace Binder;

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
        return Template::newInstance($this->getSampleBuilder(), $this->getSampleText());
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
}
