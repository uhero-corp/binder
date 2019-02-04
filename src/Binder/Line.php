<?php

namespace Binder;

interface Line
{
    /**
     * @param Entry $e
     * @return string[]
     */
    public function translate(Entry $e);

    /**
     * @return string[]
     */
    public function getKeys();
}
