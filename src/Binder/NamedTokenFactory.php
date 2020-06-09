<?php

namespace Binder;

class NamedTokenFactory implements TokenFactory
{
    /**
     * @var StringConverter
     */
    private $converter;

    /**
     * @param StringConverter $converter
     */
    public function __construct(StringConverter $converter = null)
    {
        $this->converter = $converter;
    }

    /**
     * @param string $name
     * @return Token
     */
    public function create($name)
    {
        return new NamedToken($name, $this->converter);
    }
}
