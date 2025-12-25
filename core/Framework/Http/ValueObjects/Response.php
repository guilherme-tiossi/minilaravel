<?php

namespace Core\Framework\Http\ValueObjects;

class Response
{
    public function __construct(int $httpCode, array $body)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        header('Content-Type: application/json');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        echo json_encode($body);
        exit();
    }
}
