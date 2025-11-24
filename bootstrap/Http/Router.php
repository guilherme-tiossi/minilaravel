<?php

namespace Bootstrap\Http;

use Bootstrap\Http\Enums\HttpMethod;
use Bootstrap\Http\Middleware;

class Router
{
    private static ?Router $instance = null;
    public array $routes = [
        'GET' => [
            'withParam' => [],
            'withoutParam' => []
        ],
        'POST' => [
            'withParam' => [],
            'withoutParam' => []
        ],
        'PUT' => [
            'withParam' => [],
            'withoutParam' => []
        ],
        'PATCH' => [
            'withParam' => [],
            'withoutParam' => []
        ]
    ];

    public static function getInstance(): Router
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public function addGroup(string $middleware, array $routes): void
    {
        foreach ($routes as $route) {
            $middlewares = empty($route['middlewares']) ? [$middleware] : array_merge([$middleware, $route['middlewares']]); 
            $this->addRoute(
                method: $route[0],
                uri: $route[1],
                controller: $route[2],
                methodName: $route[3],
                middlewares: $middlewares
            );
        }
    }

    public function addRoute(HttpMethod $method, string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (preg_match_all('/\{([^}]+)\}/', $uri, $matches)) {
            $params = [];
            foreach ($matches[1] as $match) {
                $params[] = $match;
            }

            $this->routes[$method->value]['withParam'][$uri] = [
                'controller' => $controller,
                'methodName' => $methodName,
                'params' => $params,
                'middlewares' => $middlewares
            ];
        } else {
            $this->routes[$method->value]['withoutParam'][$uri] = [
                'controller' => $controller,
                'methodName' => $methodName,
                'middlewares' => $middlewares
            ];
        }
    }

    public function runRoute(Request $request): HttpResponse
    {
        $uri = $request->uri;
        $method = $request->method;

        $route = $this->routes[$method->value]['withoutParam'][$uri] ?? null;

        $params = [];
        if (!$route && substr_count($uri, '/') > 1 && !empty($this->routes[$method->value]['withParam'])) {
            [$route, $params] = $this->searchRouteWithParameter($uri, $method->value);
        }

        if (!$route) {
            return new HttpResponse(404, ['message' => 'Route not found']);
        }

        if (!class_exists($route['controller'])) {
            return new HttpResponse(404, ['message' => 'Controller not found']);
        }

        $controller = new $route['controller']();
        $method = $route['methodName'];
        
        if (!method_exists($controller, $method)) {
            return new HttpResponse(404, ['message' => 'Controller method not found']);
        }

        $middlewares = $route['middlewares'];

        foreach ($middlewares as $middleware) {
            $middlewareClass = new $middleware();
            $middlewareClass->run($request);
        }

        return !empty($params) ? $controller->$method($request, ...$params) : $controller->$method($request);
    }

    private function searchRouteWithParameter(string $uri, string $method): ?array
    {
        $requestedUriArray = explode('/', substr($uri, 1));
        foreach ($this->routes[$method]['withParam'] as $definedRoute => $data) {
            if (!str_contains($definedRoute, $requestedUriArray[0])) {
                continue;
            }

            $definedRouteArray = explode('/', substr($definedRoute, 1));
            
            if (count($definedRouteArray) !== count($requestedUriArray)) {
                continue;
            }

            $params = [];
            foreach ($definedRouteArray as $position => $routeString) {
                if ($this->isParameter($routeString)) {
                    $params[] = $requestedUriArray[$position];
                }
            }

            return [$this->routes[$method]['withParam'][$definedRoute], $params];
        }

        return null;
    }

    private function isParameter(string $string): bool
    {
        return preg_match_all('/\{([^}]+)\}/', $string);
    }
}
