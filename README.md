# Binder : Simple Template Engine

Binder とは、変数の代入機能のみをサポートするシンプルなテンプレートエンジンです。
他の主要なテンプレートエンジンと異なり、制御構造 (例えば if や foreach) などの複雑な機能はありません。

Binder の基本コンセプトは、小さなテンプレートの断片を複数組み合わせてきめ細かなコンテンツ生成を行うことです。
従来のテンプレートエンジンのように 1 つの巨大なテンプレートファイルを多様な命令文で飾り付けることはありません。
ロジックと view の分離がしやすいことや、バージョン管理 (差分管理) を比較的クリーンに保てることが長所です。

## チュートリアル

一番簡単なサンプルコードを以下に記載します。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

echo Template::read("Hello, my name is {name}. I am {age} years old.")
    ->entry()
    ->set("name", "Tom")
    ->set("age", 18)
    ->render();
```

read メソッドの引数で指定された `Hello, my name is {name}. I am {age} years old.` が **「テンプレート文字列」** です。
このテンプレート文字列に含まれる `{name}` と `{age}` が **「テンプレート変数」** として解釈されます。

このコードは以下の文字列を出力します。

```
Hello, my name is Tom. I am 18 years old.
```

このライブラリでユーザーが使用する主なクラスは `Template` と `Entry` の 2 つです。
さきほどのサンプルコード内で出てきた各種メソッドについて簡単に説明します。

* Template::read() : 引数のテンプレート文字列を解析して、新しい Template オブジェクトを生成します
* Template::entry() : このテンプレートの変数に値をセットするための、新しい Entry オブジェクトを生成します
* Entry::set() : 指定された変数に値をセットします。自身の Entry オブジェクトを返り値とするため、このサンプルのようにメソッドチェーンで記述することができます
* Entry::render() : この Entry オブジェクトにセットされた内容をもとにテンプレートを変換して、その結果を返します

### Template と Entry の使い分けについて

すべての機能を Template クラスに集約せず、わざわざ Template と Entry の 2 つに分かれている理由は、ひとつのテンプレートから複数の異なる結果を出力できるようにするためです。

以下に、異なる Entry オブジェクトを複数生成する簡単な例を示します。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t  = Template::read("Hello, my name is {name}. I am {age} years old.");
$e1 = $t->entry();
$e2 = $t->entry();
$e3 = $t->entry();

$e1->set("name", "Tom")->set("age", 18);
$e2->set("name", "Sally")->set("age", 24);
$e3->set("name", "John")->set("age", 20);

echo $e1->render(), PHP_EOL;
echo $e2->render(), PHP_EOL;
echo $e3->render(), PHP_EOL;
```

このコードは以下を出力します。

```
Hello, my name is Tom. I am 18 years old.
Hello, my name is Sally. I am 24 years old.
Hello, my name is John. I am 20 years old.
```

このように 1 つの Template オブジェクトから異なる結果を次々出力するという手法は
table タグ内で tr 要素を繰り返し記述するなど、同じフォーマットのコードを複数生成するようなケースの常套手段となります。

### ブロック変数とインライン変数

例えば以下のテンプレート文字列において

```html
<div>
    <p>
        {contents}
    </p>
</div>
```

3行目のように、とある行がインデント・テンプレート変数・改行のみから成り立つ場合、その行を **「ブロック変数」** と呼びます。
それ以外のテンプレート変数は **「インライン変数」** になります。

ブロック変数とインライン変数の違いは、複数行の文字列を代入したときの挙動にあります。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t = <<<EOS
<div>
    <p>
        {contents}
    </p>
</div>
EOS;

$c = <<<EOS
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
do eiusmod tempor incididunt ut labore et dolore magna aliqua.
Ut enim ad minim veniam, quis nostrud exercitation ullamco
laboris nisi ut aliquip ex ea commodo consequat.
EOS;

echo Template::read($t)->entry()->set("contents", $c)->render(), PHP_EOL;
```

このサンプルは以下の結果を出力します。

```
<div>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
        do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco
        laboris nisi ut aliquip ex ea commodo consequat.
    </p>
</div>
```

p 要素内のテキストがインデントされていることに着目してください。
このようにブロック変数に複数行の文字列を代入した場合、インデントを保ったままで改行されます。

インライン変数では上記とは異なり、代入された文字列がそのまま出力されます。
さきほどのサンプルコードの `contents` 変数をブロック変数からインライン変数に置き換えてみましょう。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t = <<<EOS
<div>
    <p>{contents}</p>
</div>
EOS;

$c = <<<EOS
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
do eiusmod tempor incididunt ut labore et dolore magna aliqua.
Ut enim ad minim veniam, quis nostrud exercitation ullamco
laboris nisi ut aliquip ex ea commodo consequat.
EOS;

echo Template::read($t)->entry()->set("contents", $c)->render(), PHP_EOL;
```

このサンプルは以下の結果を出力します。さきほどと異なり
p 要素内のテキストがありのままの状態で適用されていることがわかります。

```
<div>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed
do eiusmod tempor incididunt ut labore et dolore magna aliqua.
Ut enim ad minim veniam, quis nostrud exercitation ullamco
laboris nisi ut aliquip ex ea commodo consequat.</p>
</div>
```

### 変数に値をセットしなかった場合の挙動

テンプレート変数に値を代入せずに出力した場合、インライン変数はその変数の前後が詰まる形となりますが、
ブロック変数は行全体がまるごとなくなります。

以下サンプルです。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t = <<<EOS
<div class="inline">{data1}</div>
<div class="block">
    {data2}
</div>
EOS;

echo Template::read($t)->entry()->render(), PHP_EOL;
```

このサンプルコードは以下を出力します。

```
<div class="inline"></div>
<div class="block">
</div>
```

`{data2}` のブロック変数が行ごと無くなっていることに注目してください。
このように、ブロック変数は行全体がひとつの変数として処理されることが特徴です。

### 変数に配列を代入した場合の挙動

各テンプレート変数には文字列だけでなく配列を代入することができます。その挙動はブロック変数とインライン変数で異なります。
配列を使用するサンプルを以下に示します。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t = <<<EOS
<div>
    <p>{contents}</p>
</div>
<div>
    <p>
        {contents}
    </p>
</div>
EOS;

$arr = ["This", "is", "a", "pen"];

echo Template::read($t)->entry()->set("contents", $arr)->render(), PHP_EOL;
```

このサンプルは以下の結果を出力します。

```
<div>
    <p>This is a pen</p>
</div>
<div>
    <p>
        This
        is
        a
        pen
    </p>
</div>
```

配列が代入されたインライン変数は、配列の各要素を空白文字 (0x20) で区切って 1 行で出力されます。
それに対してブロック変数の場合は各要素が1行ずつ出力されます。

### マークアップ言語に特化した変数記法

HTML や XML などのマークアップ言語をテンプレートとする場合、従来の変数記法に加えてコメントや
data-bind 属性をマークアップ変数として使用できます。

以下にコメントや data-bind 属性を含んだテンプレートのサンプルを示します。

```html
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{site_name}</title>
        <link rel="stylesheet" href="style.css">
        <!--{extra_css}-->
        <script src="common.js"></script>
        <!--{extra_js}-->
    </head>
    <body>
        <form method="post" action="result.php">
            <input type="text" name="q" data-bind="q_attr"><br>
            <input type="checkbox" name="r" value="1" data-bind="r_attr"> Remember me<br>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>
```

このサンプルでは、以下の文字列がテンプレート変数として処理されます。

* `{site_name}`
* `<!--{extra_css}-->`
* `<!--{extra_js}-->`
* `data-bind="q_attr"`
* `data-bind="r_attr"`

コメント形式のテンプレート変数は、通常の変数と同様にインライン変数またはブロック変数となります。
data-bind 属性の変数は通常の変数と異なり、タグ内の属性を動的に記述するのに特化しています。

このテンプレートを処理するサンプルを以下に示します。
テンプレート文字列を読み込む際に read() メソッドではなく readMarkup() を使用することに注意してください。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$t = <<<EOS
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>{site_name}</title>
        <link rel="stylesheet" href="style.css">
        <!--{extra_css}-->
        <script src="common.js"></script>
        <!--{extra_js}-->
    </head>
    <body>
        <form method="post" action="result.php">
            <input type="text" data-bind="q_attr"><br>
            <input type="checkbox" data-bind="r_attr"> Remember me<br>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>
EOS;

$cssList = [
    '<link rel="stylesheet" href="form.css">',
    '<link rel="stylesheet" href="extra.css">',
];
$qAttr = [
    "name"  => "q",
    "value" => "Previous input contents",
];
$rAttr = [
    "name"  => "r",
    "value" => "1",
    "checked",
];
$result = Template::readMarkup($t)
    ->entry()
    ->set("site_name", "SAMPLE SITE")
    ->set("extra_css", $cssList)
    ->set("q_attr", $qAttr)
    ->set("r_attr", $rAttr)
    ->render();
echo $result, PHP_EOL;
```

このサンプルは以下を出力します。

```
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>SAMPLE SITE</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="form.css">
        <link rel="stylesheet" href="extra.css">
        <script src="common.js"></script>
    </head>
    <body>
        <form method="post" action="result.php">
            <input type="text" name="q" value="Previous input contents"><br>
            <input type="checkbox" name="r" value="1" checked> Remember me<br>
            <button type="submit">Submit</button>
        </form>
    </body>
</html>
```

コメント形式のテンプレート変数について、`<!--` から `-->` までがひとつの変数として扱われていることに注目してください。
このように、コメント形式と data-bind 属性の変数を活用することで HTML の文法を valid に保ちながら各種変数を埋め込むことができます。

data-bind 属性は基本的に配列をセットして使用します。指定された配列は以下の要領で処理されます。

* キーが文字列の場合: キーを属性名・値を属性値としてタグ内の属性を出力します。特殊文字は自動的にエスケープされます
* キーが数値の場合: 値をそのまま出力します (上記サンプルの `checked` に相当)

配列ではなく文字列を指定した場合、通常のテンプレート変数と同様に、該当箇所がそのまま置換されます。

### 変数の記法をカスタマイズしたい場合

`TemplateBuilder` クラスや `Variable` クラスを使用することで、独自の変数の記法でテンプレートを処理することができます。
例えば `%var%` のように % で変数名を囲んだ文字列を変数として認識させたい場合、以下のコードで実現できます。

```php
<?php

use \Binder\TemplateBuilder;
use \Binder\Variable;

require_once("vendor/autoload.php");

$builder  = new TemplateBuilder();
$variable = new Variable("%", "%");
$builder->addSymbol($variable);
$template = $builder->build("Hello, my name is %name%. I am %age% years old.");

echo $template->entry()->set("name", "Tom")->set("age", 18)->render(), PHP_EOL;
```

ちなみに `Template::read()` や `Template::readMarkup()` で使用されているデフォルトの TemplateBuilder は以下の要領で取得することができます。

```php
<?php

use \Binder\Template;

require_once("vendor/autoload.php");

$text = "Hello, my name is {name}. I am {age} years old.";

$b1 = Template::createDefaultBuilder();
$t1 = $b1->build($text);
$t2 = Template::read($text);
var_dump($t1 == $t2); // true

$b2 = Template::createDefaultMarkupBuilder();
$t3 = $b2->build($text);
$t4 = Template::readMarkup($text);
var_dump($t3 == $t4); // true
```

## インストール

Composer からインストールできます。

動作要件: PHP 5.4 以上
