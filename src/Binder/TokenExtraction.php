<?php

namespace Binder;

class TokenExtraction
{
    /**
     * @var string
     */
    private $leftPart;

    /**
     * @var string
     */
    private $rightPart;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param Token $token
     * @param string $leftPart
     * @param string $rightPart
     */
    private function __construct($token, $leftPart, $rightPart)
    {
        $this->token     = $token;
        $this->leftPart  = $leftPart;
        $this->rightPart = $rightPart;
    }

    /**
     * @param Token $token
     * @param string $leftPart
     * @param string $rightPart
     * @return TokenExtraction
     */
    public static function done(Token $token, $leftPart, $rightPart)
    {
        return new self($token, $leftPart, $rightPart);
    }

    /**
     * @return TokenExtraction
     */
    public static function fail()
    {
        // @codeCoverageIgnoreStart
        static $instance = null;
        if ($instance === null) {
            $instance = new self(null, null, null);
        }
        // @codeCoverageIgnoreEnd
        return $instance;
    }

    /**
     * @return string
     */
    public function getLeftPart()
    {
        return $this->leftPart;
    }

    /**
     * @return string
     */
    public function getRightPart()
    {
        return $this->rightPart;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isFailure()
    {
        return ($this->token === null);
    }
}
