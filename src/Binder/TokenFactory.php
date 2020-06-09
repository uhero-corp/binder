<?php

namespace Binder;

interface TokenFactory
{
    /**
     * @param string $name
     * @return Token
     */
    public function create($name);
}
