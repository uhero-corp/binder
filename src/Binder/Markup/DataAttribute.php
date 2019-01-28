<?php

namespace Binder\Markup;

use Binder\Symbol;
use Binder\TokenExtraction;
use Binder\Variable;
use InvalidArgumentException;

class DataAttribute implements Symbol
{
    /**
     * @var Variable
     */
    private $variable;

    /**
     * @param string $attrName
     */
    public function __construct($attrName = "bind")
    {
        if (!preg_match("/\\A[a-zA-Z0-9_\\-]+\\z/", $attrName)) {
            throw new InvalidArgumentException("Invalid data attribute name: '{$attrName}'");
        }
        $this->variable = new Variable(" data-{$attrName}=\"", "\"");
    }

    /**
     * @param string $text
     * @return TokenExtraction
     */
    public function parse($text)
    {
        $result = $this->variable->parse($text);
        if ($result->isFailure()) {
            return $result;
        }

        $token = new AttributeToken($result->getToken()->getKey());
        $left  = $result->getLeftPart();
        $right = $result->getRightPart();
        return TokenExtraction::done($token, $left, $right);
    }
}
