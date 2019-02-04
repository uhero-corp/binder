<?php

namespace Binder;

class MixedLine implements Line
{
    /**
     * @var Token[]
     */
    private $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param Entry $e
     * @return string[]
     */
    public function translate(Entry $e)
    {
        $result = "";
        foreach ($this->tokens as $token) {
            $result .= $token->translate($e);
        }
        return [$result];
    }

    /**
     * @return string[]
     */
    public function getKeys()
    {
        $getTokenKeys = function (Token $t) {
            return $t->getKey();
        };
        $filterKey = function ($key) {
            return strlen($key);
        };
        return array_values(
            array_filter(
                array_map($getTokenKeys, $this->tokens),
                $filterKey
            )
        );
    }
}
