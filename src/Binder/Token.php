<?php

namespace Binder;

interface Token
{
    /**
     * @return string
     */
    public function translate(Entry $e);

    /**
     * @return string
     */
    public function getKey();

    /**
     * このトークンから Line オブジェクトを生成します。
     * このメソッドは、とある行がインデントとこのトークンのみで構成されている場合に使用されます。
     *
     * @param string $indent
     */
    public function createLine($indent);
}
