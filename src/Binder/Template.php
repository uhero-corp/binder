<?php

namespace Binder;

use Binder\Markup\Comment;
use Binder\Markup\DataAttribute;

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
     * @return TemplateBuilder
     */
    public static function createDefaultBuilder()
    {
        $builder = new TemplateBuilder();
        $builder->addSymbol(new Variable("{", "}"));
        return $builder;
    }

    /**
     * @return TemplateBuilder
     */
    public static function createDefaultMarkupBuilder()
    {
        $builder = new TemplateBuilder();
        $builder->setBreakCode("\n");
        $builder->addSymbol(new Comment("{", "}"));
        $builder->addSymbol(new DataAttribute());
        $builder->addSymbol(new Variable("{", "}"));
        return $builder;
    }

    /**
     * 指定された文字列を解析して、汎用の Template インスタンスを生成します。
     * このインスタンスは "{name}" 形式の文字列を変数として解釈します。
     *
     * @param string $text
     * @return Template
     */
    public static function read($text)
    {
        // @codeCoverageIgnoreStart
        static $builder = null;
        if ($builder === null) {
            $builder = self::createDefaultBuilder();
        }
        // @codeCoverageIgnoreEnd

        return $builder->build($text);
    }

    /**
     * 指定された文字列を解析して、マークアップ文書に特化した Template インスタンスを生成します。
     * このインスタンスは "{name}" 形式の文字列に加えて
     * "<!--{xxxx}-->" 形式のコメント文字列や data-bind 属性を変数として使用することができます。
     * 改行コードは PHP を実行している OS に関わらず LF となります。
     *
     * @param string $text
     * @return Template
     */
    public static function readMarkup($text)
    {
        // @codeCoverageIgnoreStart
        static $builder = null;
        if ($builder === null) {
            $builder = self::createDefaultMarkupBuilder();
        }
        // @codeCoverageIgnoreEnd

        return $builder->build($text);
    }

    /**
     * このテンプレートの各変数に値をセットするための、新しい
     * Entry オブジェクトを生成します。
     *
     * @return Entry
     */
    public function entry()
    {
        return new Entry($this);
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
