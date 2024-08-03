<?php
// global キーは、それらのミドルウェアがすべてのリクエストで実行されることを意味します。
return [
    'global'=>[
        \Middleware\SessionsSetupMiddleware::class,
        \Middleware\MiddlewareA::class,
        \Middleware\MiddlewareB::class,
        \Middleware\MiddlewareC::class,
        \Middleware\CSRFMiddleware::class,
    ],
    'aliases'=>[
        'auth'=>\Middleware\AuthenticatedMiddleware::class,
        'guest'=>\Middleware\GuestMiddleware::class,
    ]
];