<?php

namespace Core\Framework\Http\Traits;

use Core\Framework\Http\Request;

trait RunMiddlewares
{
    public function runMiddlewares(array $middlewareClasses, Request $request): void
    {
        $reversedMiddlewares = array_reverse($middlewareClasses);
        
        $nextMiddleware = null;
        foreach ($reversedMiddlewares as $middlewareClass) {
            $nextMiddleware = new $middlewareClass($nextMiddleware);
        }

        if ($nextMiddleware) {
            $nextMiddleware->run($request);
        }
    }
}
