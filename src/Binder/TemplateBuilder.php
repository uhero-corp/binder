<?php

namespace Binder;

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
}
