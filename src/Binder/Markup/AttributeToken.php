<?php

namespace Binder\Markup;

use Binder\Entry;
use Binder\Token;

class AttributeToken implements Token
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
     * @return string
     */
    public function getKey()
    {
        return $this->name;
    }

    /**
     * @param Entry $e
     * @return string
     */
    public function translate(Entry $e)
    {
        $content = $e->get($this->name);
        return is_array($content) ? $this->translateArray($content) : $this->translateString((string) $content);
    }

    /**
     * @param array $content
     * @return string
     */
    private function translateArray(array $content)
    {
        if (!count($content)) {
            return "";
        }

        $result = [];
        foreach ($content as $key => $value) {
            $result[] = $this->formatAttribute($key, $value);
        }
        return " " . implode(" ", $result);
    }

    /**
     * @param string|int $key
     * @param string $value
     * @return string
     */
    private function formatAttribute($key, $value)
    {
        if (is_int($key)) {
            return $value;
        }
        if ($value === null) {
            return $key;
        }
        $escaped = $this->escape($value);
        return "{$key}=\"{$escaped}\"";
    }

    /**
     * @param string $value
     * @return string
     */
    private function escape($value)
    {
        $escaped = htmlspecialchars($value);
        return preg_replace("/\\r\\n|\\r|\\n/", "&#xa;", $escaped);
    }

    /**
     * @param string $content
     * @return string
     */
    private function translateString($content)
    {
        return strlen($content) ? " " . $content : "";
    }
}
