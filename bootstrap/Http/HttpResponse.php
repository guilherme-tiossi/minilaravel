<?php

namespace Bootstrap\Http;

class HttpResponse
{
    public function __construct(int $httpCode, array $body)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');

        echo json_encode($body);
        exit();
    }
}