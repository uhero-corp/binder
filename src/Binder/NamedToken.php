<?php

namespace Binder;

class NamedToken implements Token
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var StringConverter
     */
    private $converter;

    /**
     * @param string $name
     * @param StringConverter $converter
     */
    public function __construct($name, StringConverter $converter = null)
    {
        $this->name      = $name;
        $this->converter = $converter;
    }

    /**
     * @return StringConverter
     */
    public function getStringConverter()
    {
        return ($this->converter === null) ? RawStringConverter::getInstance() : $this->converter;
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
        return $this->getStringConverter()->convert($str);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->name;
    }

    /**
     * @param string $indent
     * @return Line
     */
    public function createLine($indent)
    {
        return new BlockLine($this->name, $indent, $this->converter);
    }
}
