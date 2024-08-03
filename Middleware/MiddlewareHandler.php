<?php

namespace Middleware;

require_once __DIR__ . '/../Response/HTTPRenderer.php';

use Response\HTTPRenderer;

class MiddlewareHandler
{
    /**
     * @param Middleware[] $middlewares
     */
    public function __construct(private array $middlewares){}
    public function run(Callable $action): HTTPRenderer{
        $middlewares = array_reverse($this->middlewares);

        foreach ($middlewares as $middleware){
            $action = fn() => $middleware->handle($action);
        }

        return $action();
    }
}