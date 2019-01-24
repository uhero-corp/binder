<?php

namespace Binder;

interface Token
{
    /**
     * @return string
     */
    public function translate(Entry $e);

    /**
     * @return string
     */
    public function getKey();
}
