<?php

namespace Binder;

use InvalidArgumentException;

class Entry
{
    /**
     * @var Template
     */
    private $template;

    /**
     * @var array
     */
    private $mapping;

    /**
     * このメソッドは Template::entry() から呼び出されます。
     * ユーザーが直接使用する機会はありません。
     *
     * @param Template $t
     * @ignore
     */
    public function __construct(Template $t)
    {
        $this->template = $t;
        $this->mapping  = $t->getEmptyMapping();
    }

    /**
     * 指定されたテンプレート変数に値をセットします。
     * 第 2 引数にはスカラー値 (文字列・整数など), 配列, クロージャ (コールバック関数) を指定することができます。
     *
     * スカラー値が指定された場合は render() を実行するタイミングで文字列に変換されます。
     *
     * 配列が指定された場合は各要素を行単位または空白文字で区切って並べた結果が適用されます。(詳細仕様は README を参照してください)
     *
     * クロージャが指定された場合は render() を実行するタイミングで評価が行われ、
     * その結果として返された値がテンプレート変数の値として適用されます。
     * クロージャを呼び出す際にテンプレート変数名 ($key) を第 1 引数を指定して実行します。
     *
     * 存在しないテンプレート変数名が指定された場合は InvalidArgumentException をスローします。
     *
     * @param string $key テンプレート変数名
     * @param string|array|callable $value セットする値
     * @return Entry このオブジェクト自身
     * @throws InvalidArgumentException 存在しないテンプレート変数名が指定された場合
     */
    public function set($key, $value)
    {
        $this->validateParameter($key);
        $this->mapping[$key] = $value;
        return $this;
    }

    /**
     * 指定されたテンプレート変数名が存在している場合のみ、第 2 引数の値をそのテンプレート変数にセットします。
     * テンプレート変数が存在しない場合は何もせずにこのオブジェクト自身を返します。
     * その他の仕様は set() と同じです。
     *
     * このメソッドの効果的な利用方法は、存在するかどうか不明なテンプレート変数に対して第 2 引数でクロージャを指定することです。
     * そうすることで、もしもテンプレート変数が存在しなかった場合に不要な処理が実行されなくなります。
     *
     * @param string $key テンプレート変数名
     * @param string|array|callable $value セットする値
     * @return Entry このオブジェクト自身
     */
    public function setIfExists($key, $value)
    {
        if (array_key_exists($key, $this->mapping)) {
            $this->mapping[$key] = $value;
        }
        return $this;
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

    /**
     * この Entry にセットされた変数をテンプレートに適用し、結果を文字列として返します。
     *
     * @return string
     */
    public function render()
    {
        $result = [];
        foreach ($this->template->getLines() as $line) {
            array_splice($result, count($result), 0, $line->translate($this));
        }
        return implode($this->template->getBreakCode(), $result);
    }
}
