<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

class HandleCors implements Middleware
{
    private array $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://localhost:3002'
    ];

    public function __construct(
        private ?Middleware $next = null
    ) {
    }

    public function run(Request $request): ?Response
    {
        if ($request->origin && in_array($request->origin, $this->allowedOrigins, true)) {
            header('Access-Control-Allow-Origin: ' . $request->origin);
        }

        if ($request->method === HttpMethod::OPTIONS) {        
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            return new Response(204, []);
        }

        if ($this->next) {
            return $this->next->run($request);
        }

        return null;
    }
}