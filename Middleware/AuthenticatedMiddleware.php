<?php

namespace Middleware;

require_once __DIR__ . '/../Helpers/Authenticate.php';
require_once __DIR__ . '/Middleware.php';
require_once __DIR__ . '/../Response/FlashData.php';
require_once __DIR__ . '/../Response/HTTPRenderer.php';
require_once __DIR__ . '/../Response/Render/RedirectRenderer.php';


use Helpers\Authenticate;
use Middleware\Middleware;
use Response\FlashData;
use Response\HTTPRenderer;
use Response\Render\RedirectRenderer;

class AuthenticatedMiddleware implements Middleware
{
    public function handle(callable $next): HTTPRenderer
    {
        error_log('Running authentication check...');
        if(!Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Must login to view this page.');
            return new RedirectRenderer('login');
        }

        return $next();
    }
}