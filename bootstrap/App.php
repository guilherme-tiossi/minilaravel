<?php

namespace Bootstrap;

require __DIR__ . '/../routes/api.php';
require __DIR__ . '/../vendor/autoload.php';

use Bootstrap\Http\Router;

class App 
{
    public function run(): void
    {
        $host = "0.0.0.0";
        $port = "8080";
        $docroot = __DIR__ . "/../public";

        echo "Starting server at http://{$host}:{$port}\n";
        echo "Serving from: {$docroot}\n";

        $cmd = sprintf("php -S %s:%s -t %s", $host, $port, escapeshellarg($docroot));

        passthru($cmd);
    }

    public function help(): void
    {
        echo "List of available commands:\n";
        echo "php miniartisan run # starts the server\n";
        echo "php miniartisan help # lists available commands\n";
        exit();
    }

    public function routes(): void
    {
        $router = Router::getInstance();

        echo "List of routes:\n";
        // melhorar isso, muitos loops
        foreach ($router->routes as $httpMethod => $paramDefinitions) {
            foreach ($paramDefinitions as $routes) {
                if (!$routes) continue;
                foreach ($routes as $route => $routeData) {
                    $controller = substr($routeData['controller'], strrpos($routeData['controller'], "\\") + 1);
                    $method = $routeData['methodName'];
                    echo $httpMethod . ' - ' . $route . ' - ' . $controller . '::' . $method . "() \n";
                }
            }
        }
        exit();
    }

    public function error(): void
    {
        echo "FATAL MINIARTISAN ERROR:\n";
        echo "Unavailable command!\n";
        exit();
    }
}
