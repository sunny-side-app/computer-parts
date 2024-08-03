<?php
// リクエストライフサイクルの開始時にセッションを設定するミドルウェアです。この方法では、セッションドライバーや他の設定を一元的な場所から変更でき、すべてのルートに影響します。session_start(); 部分は index からここに移動されました。
namespace Middleware;

require_once __DIR__ . '/../Response/HTTPRenderer.php';
require_once __DIR__ . '/Middleware.php';

use Response\HTTPRenderer;
use Middleware\Middleware;

class SessionsSetupMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Setting up sessions...');
        session_start();
        // セッションに関するその他の処理を行います

        // 次のミドルウェアに進みます
        return $next();
    }
}