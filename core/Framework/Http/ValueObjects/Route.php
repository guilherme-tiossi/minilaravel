<?php

namespace Core\Framework\Http\ValueObjects;

use Core\Framework\Http\Enums\HttpMethod;

class Route
{
    public function __construct(
        public HttpMethod $httpMethod,
        public string $uri,
        public string $controller,
        public string $method,
        public ?array $middlewares = [],
        public ?array $params = []
    ) {
    }
}
