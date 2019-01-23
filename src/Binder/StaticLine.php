<?php

namespace Binder;

class StaticLine implements Line
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
     * @param Entry $e
     * @return string[]
     */
    public function translate(Entry $e)
    {
        return [$this->text];
    }

    /**
     * @return string[]
     */
    public function getKeys()
    {
        return [];
    }
}
