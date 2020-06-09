<?php

namespace Binder;

interface StringConverter
{
    /**
     * @param string $str
     * @return string
     */
    public function convert($str);
}
