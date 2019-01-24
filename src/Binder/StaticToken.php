<?php

namespace Binder;

class StaticToken implements Token
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function translate(Entry $e)
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return "";
    }
}
