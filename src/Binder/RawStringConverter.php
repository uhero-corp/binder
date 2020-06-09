<?php

namespace Binder;

class RawStringConverter implements StringConverter
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
     * このクラスの唯一のインスタンスを取得します。
     *
     * @return RawStringConverter
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
     * @param string $str
     * @return string
     */
    public function convert($str)
    {
        return (string) $str;
    }
}
