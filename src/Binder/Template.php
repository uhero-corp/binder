<?php

namespace Binder;

class Template
{
    /**
     * @var Line[]
     */
    private $lines;

    /**
     * @var string[]
     */
    private $keys;

    /**
     * @var string
     */
    private $breakCode;

    /**
     * Template クラスは TemplateBuilder オブジェクトを使用して構築します
     */
    private function __construct()
    {
    }

    /**
     * @param TemplateBuilder $builder
     * @param string $text
     * @return Template
     * @ignore
     */
    public static function newInstance(TemplateBuilder $builder, $text)
    {
        $symbols   = $builder->getSymbols();
        $breakCode = $builder->getBreakCode();

        $parser      = new TokenParser($symbols);
        $lines       = preg_split("/\\r\\n|\\r|\\n/", $text);
        $lineObjects = [];
        $keys        = [];
        foreach ($lines as $line) {
            $nextLine      = self::parseLine($parser, $line);
            $lineObjects[] = $nextLine;
            array_splice($keys, count($keys), 0, $nextLine->getKeys());
        }

        $instance            = new self();
        $instance->lines     = $lineObjects;
        $instance->keys      = $keys;
        $instance->breakCode = strlen($breakCode) ? $breakCode : PHP_EOL;
        return $instance;
    }

    /**
     * @param TokenParser $parser
     * @param string $line
     * @return Line
     */
    private static function parseLine(TokenParser $parser, $line)
    {
        $matched = [];
        preg_match("/\\A([ \\t]*)/", $line, $matched);
        $indent  = $matched[1];
        $text    = substr($line, strlen($indent));
        $tokens  = $parser->parse($text);
        switch (count($tokens)) {
            case 0:
                return new StaticLine($indent);
            case 1:
                return $tokens[0]->createLine($indent);
            default:
                return new MixedLine(array_merge([new StaticToken($indent)], $tokens));
        }
    }

    /**
     * @return Line[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @return array
     */
    public function getEmptyMapping()
    {
        return array_fill_keys($this->keys, null);
    }

    /**
     * 結果を出力する時に各行の末尾に付与される改行文字を取得します。
     * 明示的に指定しない限り、システム依存の改行コードである LF (0x0A) または CRLF (0x0D 0x0A) を返します。
     *
     * @return string
     */
    public function getBreakCode()
    {
        return $this->breakCode;
    }
}
