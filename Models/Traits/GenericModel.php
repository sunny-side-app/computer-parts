<?php

namespace Models\Traits;

// PHP のトレイトは、あたかもメソッドをクラス間でコピー＆ペーストしているように、クラスにコードブロックを挿入する手段を提供します。
// これは、単一継承の原則に従いながらコードの再利用を可能にするため、または単純にコードの重複を避けて挿入するために使われます。
// https://www.php.net/manual/ja/language.oop5.traits.php

trait GenericModel
{
    public function toArray(): array{
        return (array) $this;
    }

    public function toString(): string{
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}


