<?php

namespace Core\Framework;

# POR ENQUANTO MANTEMOS ISSO AQUI - ESTÁ ERRADO POIS NADA DE APPLICATION DEVE CONTAMINAR
# O FRAMEWORK, MAS ASSIM QUE POSSÍVEL FAREMOS UM ROUTESERVICEPROVIDER
require __DIR__ . '/../Application/routes/api.php';

use Core\Framework\Http\Router;

class App 
{
    public function run(): void
    {
        $host = "0.0.0.0";
        $port = "8080";
        $docroot = __DIR__ . "/public";

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
        echo "List of routes:\n";
        Router::routes();
        exit();
    }

    public function error(): void
    {
        echo "FATAL MINIARTISAN ERROR:\n";
        echo "Unavailable command!\n";
        exit();
    }
}
