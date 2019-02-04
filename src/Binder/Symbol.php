<?php

namespace Binder;

interface Symbol
{
    /**
     * @param string $text
     * @return TokenExtraction
     */
    public function parse($text);
}
