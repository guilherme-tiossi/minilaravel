<?php

namespace Core\Framework\Http\Services\RouteResolver;

use Core\Shared\Exceptions\AppException;

class RouteResolver
{
    public function resolve(InputDto $input): OutputDto
    {
        $httpMethod = $input->httpMethod;
        $uri = $input->uri;

        $route = $input->routes[$httpMethod->value]['withoutParam'][$uri] ?? null;

        $params = [];
        if (!$route && substr_count($input->uri, '/') > 1 && !empty($input->routes[$httpMethod->value]['withParam'])) {
            [$route, $params] = self::searchRouteWithParameter($uri, $httpMethod->value, $input->routes);
        }
        $routeMiddlewares = $route->middlewares ?? [];

        if (!$route) {
            throw new AppException(404, 'Route not found');
        }

        if (!class_exists($route->controller)) {
            throw new AppException(404, 'Controller not found');
        }

        $controller = $input->container->make($route->controller);
        $method = $route->method;

        if (!method_exists($controller, $method)) {
            throw new AppException(404, 'Controller method not found');
        }

        return new OutputDto(
            controller: $controller,
            method: $method,
            params: $params,
            middlewares: $routeMiddlewares
        );
    }

    private function searchRouteWithParameter(string $uri, string $method, array $routes): ?array
    {
        $requestedUriArray = explode('/', substr($uri, 1));
        foreach ($routes[$method]['withParam'] as $definedRoute => $data) {
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

            return [$routes[$method]['withParam'][$definedRoute], $params];
        }

        return null;
    }

    private function isParameter(string $string): bool
    {
        return preg_match_all('/\{([^}]+)\}/', $string);
    }
}
