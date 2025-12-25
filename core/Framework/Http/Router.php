<?php

namespace Core\Framework\Http;

use Core\Framework\Container;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Services\RouteResolver\RouteResolver;
use Core\Framework\Http\Services\RouteResolver\InputDto as RouteResolverInputDto;
use Core\Framework\Http\Traits\RunMiddlewares;
use Core\Framework\Http\ValueObjects\Request;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Route;

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
                httpMethod: $route[0],
                uri: $route[1],
                controller: $route[2],
                method: $route[3],
                middlewares: $middlewares
            );
        }
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
                    $method = $routeData['method'];
                    echo $httpMethod . ' - ' . $route . ' - ' . $controller . '::' . $method . "() \n";
                }
            }
        }
    }

    public static function dispatch(Request $request, Container $container): Response
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        $uri = $request->uri;
        $httpMethod = $request->method;

        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve(new RouteResolverInputDto(
            container: $container,
            httpMethod: $httpMethod,
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

    public static function addRoute(HttpMethod $httpMethod, string $uri, string $controller, string $method, ?array $middlewares = null): void
    {
        if (!self::$instance) {
            self::$instance = new Router();
        }

        $routeObject = new Route(
            httpMethod: $httpMethod,
            uri: $uri,
            controller: $controller,
            method: $method,
            middlewares: $middlewares
        );

        if (preg_match_all('/\{([^}]+)\}/', $uri, $matches)) {
            $params = [];
            foreach ($matches[1] as $match) {
                $params[] = $match;
            }

            $routeObject->params = $params;
            self::$instance->routes[$httpMethod->value]['withParam'][$uri] = $routeObject;
        } else {
            self::$instance->routes[$httpMethod->value]['withoutParam'][$uri] = $routeObject;
        }
    }
}
