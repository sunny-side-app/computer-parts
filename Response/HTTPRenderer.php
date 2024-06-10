<?php

namespace Response;

// HTTPRenderer オブジェクトを使用するシステムは、getContent() から文字列を取得するだけを期待しているため、各実装者がこのコンテンツの作成方法をさらに詳細に定義します。例えば、HTMLRenderer は MVC のアプローチを採用します。モデル、ビュー、コントローラが分離され、コントローラが Renderer クラスのインスタンスを作成して返す役割を果たします。コントローラは、OOP クラスやデータベーススキーマにマッピングされたデータなどのモデルを使ってデータを準備し、このデータをビューに渡してコンテンツを作成します。

interface HTTPRenderer {
    // getFields は適切な HTTP レスポンスを設定する役割を担う
    public function getFields(): array;
    // getContent は HTTP レスポンスボディのコンテンツの文字列を返す
    public function getContent(): string;
}