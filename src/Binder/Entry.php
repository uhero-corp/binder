<?php

namespace Binder;

use InvalidArgumentException;

class Entry
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * @param string[] $keys
     */
    public function __construct(array $keys)
    {
        $this->mapping = array_fill_keys($keys, null);
    }

    /**
     * 指定されたテンプレート変数に値をセットします。
     * 存在しないテンプレート変数名が指定された場合は InvalidArgumentException をスローします。
     *
     * @param string $key テンプレート変数名
     * @param mixed $value セットする値
     * @throws InvalidArgumentException 存在しないテンプレート変数名が指定された場合
     */
    public function set($key, $value)
    {
        $this->validateParameter($key);
        $this->mapping[$key] = $value;
    }

    /**
     * 指定されたテンプレート変数に現在セットされている値を取得します。
     * もしも値がセットされていない場合は null を返します。
     * 存在しないテンプレート変数名が指定された場合は InvalidArgumentException をスローします。
     *
     * @param string $key テンプレート変数名
     * @return mixed このテンプレート変数にセットされた値。存在しない場合は null
     * @throws InvalidArgumentException 存在しないテンプレート変数名が指定された場合
     */
    public function get($key)
    {
        $this->validateParameter($key);
        return $this->mapping[$key];
    }

    /**
     * 定義されているテンプレート変数名の一覧を取得します。
     *
     * @return string[] テンプレート変数名の一覧を列挙した文字列配列
     */
    public function keys()
    {
        return array_keys($this->mapping);
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     */
    private function validateParameter($key)
    {
        if (!array_key_exists($key, $this->mapping)) {
            throw new InvalidArgumentException("Invalid parameter name: {$key}");
        }
    }
}
