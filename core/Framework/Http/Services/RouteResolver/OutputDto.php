<?php

namespace Core\Framework\Http\Services\RouteResolver;

class OutputDto
{
    public function __construct(
        public object $controller,
        public string $method,
        public array $params = [],
        public array $middlewares = []
    ) {
    }
}