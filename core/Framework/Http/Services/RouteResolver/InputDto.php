<?php

namespace Core\Framework\Http\Services\RouteResolver;

use Core\Framework\Container;
use Core\Framework\Http\Enums\HttpMethod;

class InputDto
{
    public function __construct(
        public Container $container,
        public HttpMethod $httpMethod,
        public string $uri,
        public array $routes
    ) {
    }
}