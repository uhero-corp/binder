<?php

namespace Binder;

class TokenParser
{
    /**
     * @var Symbol[]
     */
    private $symbols;

    /**
     * @param Symbol[] $symbols
     */
    public function __construct(array $symbols)
    {
        $this->symbols = $symbols;
    }

    /**
     * @param string $text
     * @return Token[]
     */
    public function parse($text)
    {
        if (!strlen($text)) {
            return [];
        }

        foreach ($this->symbols as $s) {
            $extraction = $s->parse($text);
            if ($extraction->isFailure()) {
                continue;
            }
            $token = [$extraction->getToken()];
            $left  = $this->parse($extraction->getLeftPart());
            $right = $this->parse($extraction->getRightPart());
            return array_merge($left, $token, $right);
        }
        return [new StaticToken($text)];
    }
}
