<?php

namespace Binder;

use LogicException;

class TemplateBuilder
{
    /**
     * @var string
     */
    private $breakCode;

    /**
     * @var Symbol[]
     */
    private $symbols;

    public function __construct()
    {
        $this->breakCode = null;
        $this->symbols   = [];
    }

    /**
     * @param string $breakCode
     */
    public function setBreakCode($breakCode)
    {
        $this->breakCode = $breakCode;
    }

    /**
     * @return string
     */
    public function getBreakCode()
    {
        return $this->breakCode;
    }

    /**
     * @param Symbol $s
     */
    public function addSymbol(Symbol $s)
    {
        $this->symbols[] = $s;
    }

    /**
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * @param string $text
     * @return Template
     * @throws LogicException Symbol オブジェクトが何も登録されていない場合
     */
    public function build($text)
    {
        if (!count($this->symbols)) {
            throw new LogicException("No Symbol object is added");
        }

        return Template::newInstance($this, $text);
    }
}
