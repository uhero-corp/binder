<?php

namespace Binder\Markup;

use Binder\StringConverter;

class EscapedStringConverter implements StringConverter
{
    /**
     * このクラスはシングルトンです。
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return EscapedStringConverter
     */
    public static function getInstance()
    {
        // @codeCoverageIgnoreStart
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        // @codeCoverageIgnoreEnd

        return $instance;
    }

    /**
     * 与えられた文字列を XML や HTML として出力させるためのエスケープ処理を施します。
     * '<' や '"' などの特殊文字のエスケープだけでなく、改行文字 (CRLF, CR, LF) についても文字参照 ("&#xa;") に変換されます。
     * (改行文字により意図しないインデントが挿入されるのを防ぐため)
     *
     * @param string $str
     * @return string
     */
    public function convert($str)
    {
        return preg_replace("/\\r\\n|\\r|\\n/", "&#xa;", htmlspecialchars($str));
    }
}
