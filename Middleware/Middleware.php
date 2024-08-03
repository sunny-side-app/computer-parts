<?php

// すべてのミドルウェアが実装する必要があるミドルウェアインターフェースです。このアプリケーションでは、ミドルウェアが $_POST、$_GET、$_COOKIE、$_SESSION などに直接アクセスするため、Request を渡さないことに注意してください。これは、コードをあまりリファクタリングせず、小規模なアプリケーションでは問題ないためです。

// ただし、大規模なコードベースやフレームワークでは、リクエストオブジェクトが作成され、ミドルウェアや最終的なルートに渡され、ルートは $request オブジェクトを取り入れます。カプセル化の利点に加えて、依存性注入が可能になり、例えば、テスト中に偽の $request を生成してアプリケーション全体や個々のミドルウェアを異なる $request 状況に対してテストすることができます。実際のシナリオでは、コードベースがグローバルデータで動作することは非常に混乱しやすく、避けるべきです。

namespace Middleware;

require_once __DIR__ . '/../Response/HTTPRenderer.php';

use Response\HTTPRenderer;

interface Middleware{
    public function handle(Callable $next): HTTPRenderer;
}