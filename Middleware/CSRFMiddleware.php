<?php

// $_SESSION から csrf_token を取得するか、存在しない場合は新しいトークンを生成してセッションデータに添付する役割を担います。次に、現在の HTTP メソッドが GET メソッドでない場合、csrf_token をチェックして検証します。

namespace Middleware;

require_once __DIR__ . '/../Response/FlashData.php';
require_once __DIR__ . '/../Response/HTTPRenderer.php';
require_once __DIR__ . '/../Response/Render/RedirectRenderer.php';

use Response\FlashData;
use Response\HTTPRenderer;
use Response\Render\RedirectRenderer;

class CSRFMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        // セッションにCSRFトークンが存在するかチェックします
        if (!$_SESSION['csrf_token']) {
            // 32個のランダムバイトを生成し、16進数に変換してCSRFトークンとしてセッションに格納します
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $token = $_SESSION['csrf_token'];

        // 非GETリクエストのCSRFトークンをチェックします
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            if ($_POST['csrf_token'] !== $token) {
                FlashData::setFlashData('error', 'Access has been denied.');
                return new RedirectRenderer('random/part');
            }
        }

        return $next();
    }
}