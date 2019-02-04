<?php

namespace Binder\Markup;

use Binder\NamedToken;
use Binder\Symbol;
use Binder\TokenExtraction;
use InvalidArgumentException;

class Comment implements Symbol
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
     * @throws InvalidArgumentException
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
        return $this->find($text, 0);
    }

    /**
     * @param string $text
     * @return TokenExtraction
     */
    private function find($text, $offset)
    {
        // Checking comment start
        $indexCS = strpos($text, "<!--", $offset);
        if ($indexCS === false) {
            return TokenExtraction::fail();
        }

        // Skipping whitespace
        $indexWS1 = $indexCS + 4; // Skipping "<!--"
        $ws1      = $this->getWhitespace($text, $indexWS1);

        // Checking prefix
        $indexPrefix = $indexWS1 + strlen($ws1);
        $prefix      = $this->prefix;
        $prefixLen   = strlen($prefix);
        if (substr($text, $indexPrefix, $prefixLen) !== $prefix) {
            return $this->find($text, $indexPrefix);
        }

        // Checking key
        $indexKey = $indexPrefix + $prefixLen;
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

        // Skipping whitespace
        $indexWS2 = $indexSuffix + $suffixLen;
        $ws2      = $this->getWhitespace($text, $indexWS2);

        // Checking comment end
        $indexCE = $indexWS2 + strlen($ws2);
        if (substr($text, $indexCE, 3) !== "-->") {
            return $this->find($text, $indexCE);
        }

        $leftPart  = substr($text, 0, $indexCS);
        $rightPart = substr($text, $indexCE + 3);
        return TokenExtraction::done(new NamedToken($key), $leftPart, $rightPart);
    }

    /**
     * @param string $text
     * @param int $offset
     * @return string
     */
    private function getWhitespace($text, $offset)
    {
        $matched = [];
        preg_match("/\\A[ \\t]*/", substr($text, $offset), $matched);
        return $matched[0];
    }
}
