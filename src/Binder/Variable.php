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
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @param string $prefix
     * @param string $suffix
     * @param TokenFactory $factory
     */
    public function __construct($prefix, $suffix, TokenFactory $factory = null)
    {
        if (!strlen($prefix)) {
            throw new InvalidArgumentException("Prefix is required");
        }
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->tokenFactory = $factory;
    }

    /**
     * @return TokenFactory
     */
    public function getTokenFactory()
    {
        return $this->tokenFactory;
    }

    /**
     * @param string $key
     * @return Token
     */
    private function createToken($key)
    {
        $factory = $this->tokenFactory;
        return ($factory === null) ? new NamedToken($key) : $factory->create($key);
    }

    /**
     * @param string $text
     * @return TokenExtraction
     */
    public function parse($text)
    {
        return $this->find($text, 0);
    }

    /**
     * @param string $text
     * @param int $offset
     * @return TokenExtraction
     */
    private function find($text, $offset)
    {
        // Checking prefix
        $indexPrefix = strpos($text, $this->prefix, $offset);
        if ($indexPrefix === false) {
            return TokenExtraction::fail();
        }

        // Checking key
        $matched  = [];
        $indexKey = $indexPrefix + strlen($this->prefix);
        if (!preg_match("/\\A([a-zA-Z0-9_\\-]+)/", substr($text, $indexKey), $matched)) {
            return $this->find($text, $indexKey);
        }
        $key = $matched[1];

        // Checking suffix
        $suffix      = $this->suffix;
        $indexSuffix = $indexKey + strlen($key);
        $suffixLen   = strlen($suffix);
        if (substr($text, $indexSuffix, $suffixLen) !== $suffix) {
            return $this->find($text, $indexSuffix);
        }

        $leftPart  = substr($text, 0, $indexPrefix);
        $rightPart = substr($text, $indexSuffix + $suffixLen);
        return TokenExtraction::done($this->createToken($key), $leftPart, $rightPart);
    }
}
