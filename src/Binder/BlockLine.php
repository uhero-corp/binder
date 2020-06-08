<?php

namespace Binder;

class BlockLine implements Line
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $indent;

    /**
     * @var StringConverter
     */
    private $converter;

    /**
     * @param string $key
     * @param string $indent
     * @param StringConverter $converter
     */
    public function __construct($key, $indent, StringConverter $converter = null)
    {
        $this->key    = $key;
        $this->indent = $indent;
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
     * @return string[]
     */
    public function translate(Entry $e)
    {
        return $this->translateContent($e->get($this->key));
    }

    /**
     * @param array|string $content
     * @return string[]
     */
    private function translateContent($content)
    {
        return is_array($content) ? $this->translateArray($content) : $this->translateString((string) $content);
    }

    /**
     * @param array $content
     * @return string[]
     */
    private function translateArray(array $content)
    {
        $result = [];
        foreach ($content as $item) {
            $result = array_merge($result, $this->translateContent($item));
        }
        return $result;
    }

    /**
     * @param string $str
     * @return string[]
     */
    private function translateString($str)
    {
        $value = trim($str, "\r\n");
        if (!strlen($value)) {
            return [];
        }

        $indent    = $this->indent;
        $converter = $this->getStringConverter();
        $lines     = preg_split("/\\r\\n|\\r|\\n/", $value);
        $addIndent = function ($line) use ($indent, $converter) {
            return $indent . $converter->convert($line);
        };
        return array_map($addIndent, $lines);
    }

    /**
     * @return string[]
     */
    public function getKeys()
    {
        return [$this->key];
    }
}
