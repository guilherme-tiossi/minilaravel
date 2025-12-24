<?php

namespace Core\Framework\Http\Traits;

use Core\Framework\Container;
use Core\Framework\Http\Request;

trait RunMiddlewares
{
    // usar um DTO aqui
    public function runMiddlewares(array $middlewareClasses, Request $request, Container $container): void
    {
        $reversedMiddlewares = array_reverse($middlewareClasses);
        
        $nextMiddleware = null;
        foreach ($reversedMiddlewares as $middlewareClass) {
            $nextMiddleware = $container->make($middlewareClass, ['next' => $nextMiddleware]);
        }

        if ($nextMiddleware) {
            $nextMiddleware->run($request);
        }
    }
}
