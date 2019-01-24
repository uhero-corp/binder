<?php

namespace Binder;

class NamedToken implements Token
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param Entry $e
     * @return string
     */
    public function translate(Entry $e)
    {
        return $this->translateContent($e->get($this->name));
    }

    /**
     * @param array|string $content
     * @return string
     */
    private function translateContent($content)
    {
        return is_array($content) ? $this->translateArray($content) : $this->translateString((string) $content);
    }

    /**
     * @param array $content
     * @return string
     */
    private function translateArray(array $content)
    {
        $result = [];
        foreach ($content as $str) {
            $result[] = $this->translateContent($str);
        }
        return implode(" ", $result);
    }

    /**
     * @param string $str
     * @return string
     */
    private function translateString($str)
    {
        return $str;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->name;
    }

}
