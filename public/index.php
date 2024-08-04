<?php

require_once __DIR__ . '/../Middleware/AuthenticatedMiddleware.php';
require_once __DIR__ . '/../Middleware/GuestMiddleware.php';
require_once __DIR__ . '/../Middleware/MiddlewareA.php';
require_once __DIR__ . '/../Middleware/MiddlewareB.php';
require_once __DIR__ . '/../Middleware/MiddlewareC.php';
require_once __DIR__ . '/../Middleware/MiddlewareHandler.php';
require_once __DIR__ . '/../Middleware/SessionsSetupMiddleware.php';
require_once __DIR__ . '/../Middleware/CSRFMiddleware.php';
require_once __DIR__ . '/../Middleware/SignatureValidationMiddleware.php';

use \Middleware\AuthenticatedMiddleware;
use \Middleware\GuestMiddleware;
use \Middleware\MiddlewareA;
use \Middleware\MiddlewareB;
use \Middleware\MiddlewareC;
use \Middleware\MiddlewareHandler;
use \Middleware\SessionsSetupMiddleware;
use \Middleware\CSRFMiddleware;
use \Middleware\SignatureValidationMiddleware;

// index はアプリケーションのエントリーポイント。初期設定を行った後、適切なルートコールバックを呼び出して Renderer を取得し、データをレンダリングして HTTP レスポンスとして返す作業を行う。ミドルウェアを使用するように更新済み。チェーン内の最後の呼び出し可能なものがルート呼び出し可能関数そのものであることに注目。
// session_start();
spl_autoload_extensions(".php");
spl_autoload_register();
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/..'));

$DEBUG = true;
// header("Access-Control-Allow-Origin: *");

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|html)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

// ルートを読み込みます。
$routes = include('Routing/routes.php');

// リクエストURIを解析してパスだけを取得します。
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// ルートにパスが存在するかチェックする
if (isset($routes[$path])) {
    // ルートの取得
    $route = $routes[$path];

    try{
    
        if(!($route instanceof Routing\Route)) throw new InvalidArgumentException("Invalid route type");

        // 配列連結ミドルウェア
        $middlewareRegister = include('Middleware/middleware-register.php');
        // $middlewares = $middlewareRegister['global'];
        // $middlewareHandler = new \Middleware\MiddlewareHandler($middlewares);
        $middlewares = array_merge($middlewareRegister['global'], array_map(fn ($routeAlias) => $middlewareRegister['aliases'][$routeAlias], $route->getMiddleware()));

        $middlewareHandler = new \Middleware\MiddlewareHandler(array_map(fn($middlewareClass) => new $middlewareClass(), $middlewares));

        // // コールバックを呼び出してrendererを作成します。
        // $renderer = $routes[$path]();
        // チェーンの最後のcallableは、HTTPRendererを返す現在の$route callableとなります。
        $renderer = $middlewareHandler->run($route->getCallback());

        // ヘッダーを設定します。Ex) renderer: HTMLRenderer
        foreach ($renderer->getFields() as $name => $value) {
            // ヘッダーに対する単純な検証を実行します。
            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
            } else {
                // ヘッダー設定に失敗した場合、ログに記録するか処理します。
                // エラー処理によっては、例外をスローするか、デフォルトのまま続行することもできます。
                http_response_code(500);
                if($DEBUG) print("Failed setting header - original: '$value', sanitized: '$sanitized_value'");
                exit;
            }

            print($renderer->getContent());
        }
    }
    catch (Exception $e){
        http_response_code(500);
        print("Internal error, please contact the admin.<br>");
        if($DEBUG) print($e->getMessage());
    }
} else {
    // マッチするルートがない場合、404エラーを表示します。
    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";
}