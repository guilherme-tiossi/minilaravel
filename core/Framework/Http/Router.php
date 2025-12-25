<?php

namespace Core\Framework\Http;

use Core\Framework\Container;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Services\RouteResolver;
use Core\Framework\Http\Services\InputDto as RouteResolverInputDto;
use Core\Framework\Http\Traits\RunMiddlewares;

# refatorar esse cara assim que possível, está muy gramde
# passar lógica de singleton para uma classe reutilizável e só fazer router implementar ela
class Router
{
    use RunMiddlewares;

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

    public static function group(string $middleware, array $routes): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        foreach ($routes as $route) {
            $middlewares = empty($route['middlewares']) ? [$middleware] : array_merge([$middleware, $route['middlewares']]);
            self::$instance->addRoute(
                method: $route[0],
                uri: $route[1],
                controller: $route[2],
                methodName: $route[3],
                middlewares: $middlewares
            );
        }
    }

    public static function get(string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        self::$instance->addRoute(HttpMethod::GET, $uri, $controller, $methodName, $middlewares);
    }

    public static function post(string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        self::$instance->addRoute(HttpMethod::POST, $uri, $controller, $methodName, $middlewares);
    }

    public static function patch(string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        self::$instance->addRoute(HttpMethod::PATCH, $uri, $controller, $methodName, $middlewares);
    }

    public static function put(string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        self::$instance->addRoute(HttpMethod::PUT, $uri, $controller, $methodName, $middlewares);
    }

    public static function delete(string $uri, string $controller, string $methodName, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        self::$instance->addRoute(HttpMethod::DELETE, $uri, $controller, $methodName, $middlewares);
    }

    public static function routes(): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        foreach (self::$instance->routes as $httpMethod => $paramDefinitions) {
            foreach ($paramDefinitions as $routes) {
                if (!$routes) continue;
                foreach ($routes as $route => $routeData) {
                    $controller = substr($routeData['controller'], strrpos($routeData['controller'], "\\") + 1);
                    $method = $routeData['methodName'];
                    echo $httpMethod . ' - ' . $route . ' - ' . $controller . '::' . $method . "() \n";
                }
            }
        }
    }

    public static function dispatch(Request $request, Container $container): HttpResponse
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        $uri = $request->uri;
        $method = $request->method;

        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve(new RouteResolverInputDto(
            container: $container,
            method: $method,
            uri: $uri,
            routes: self::$instance->routes
        ));

        $controller = $resolvedRoute->controller;
        $method = $resolvedRoute->method;
        $params = $resolvedRoute->params;
        $middlewares = $resolvedRoute->middlewares;

        self::$instance->runMiddlewares($middlewares, $request, $container);
        return !empty($params) ? $controller->$method($request, ...$params) : $controller->$method($request);
    }

    private function addRoute(HttpMethod $method, string $uri, string $controller, string $methodName, ?array $middlewares = null): void
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
}
