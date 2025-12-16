<?php

namespace Core\Application\Http\Middlewares;

use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Middleware;
use Core\Framework\Http\Request;

class HandleCors implements Middleware
{
    private array $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://localhost:3002'
    ];

    public function run(Request $request): void
    {
        if ($request->origin && in_array($request->origin, $this->allowedOrigins, true)) {
            header('Access-Control-Allow-Origin: ' . $request->origin);
        }

        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        if ($request->method === HttpMethod::OPTIONS) {
            http_response_code(204);
            exit();
        }
    }
}