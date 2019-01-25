<?php

namespace Binder;

use InvalidArgumentException;

class Variable implements Symbol
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct($prefix, $suffix)
    {
        if (!strlen($prefix)) {
            throw new InvalidArgumentException("Prefix is required");
        }
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    /**
     * @param string $text
     * @return TokenExtraction
     */
    public function parse($text)
    {
        // Checking prefix
        $indexPrefix = strpos($text, $this->prefix);
        if ($indexPrefix === false) {
            return TokenExtraction::fail();
        }

        // Checking key
        $matched  = [];
        $indexKey = $indexPrefix + strlen($this->prefix);
        if (!preg_match("/\\A([a-zA-Z0-9_\\-]+)/", substr($text, $indexKey), $matched)) {
            return TokenExtraction::fail();
        }
        $key = $matched[1];

        // Checking suffix
        $suffix      = $this->suffix;
        $indexSuffix = $indexKey + strlen($key);
        $suffixLen   = strlen($suffix);
        if (substr($text, $indexSuffix, $suffixLen) !== $suffix) {
            return TokenExtraction::fail();
        }

        $leftPart  = substr($text, 0, $indexPrefix);
        $rightPart = substr($text, $indexSuffix + $suffixLen);
        return TokenExtraction::done(new NamedToken($key), $leftPart, $rightPart);
    }
}
