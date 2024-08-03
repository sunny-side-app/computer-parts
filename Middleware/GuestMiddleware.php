<?php

namespace Middleware;

require_once __DIR__ . '/../Helpers/Authenticate.php';
require_once __DIR__ . '/../Response/HTTPRenderer.php';
require_once __DIR__ . '/../Response/Render/RedirectRenderer.php';

use Helpers\Authenticate;
use Response\HTTPRenderer;
use Response\Render\RedirectRenderer;

class GuestMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Running authentication check...');
        // ユーザーがログインしている場合は、メッセージなしでランダムパーツのページにリダイレクトします
        if(Authenticate::isLoggedIn()){
            return new RedirectRenderer('random/part');
        }

        return $next();
    }
}