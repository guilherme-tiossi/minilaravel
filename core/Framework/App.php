<?php

namespace Core\Framework;

require __DIR__ . '/helpers.php';

use Core\Framework\Http\Router;
use Core\Framework\Providers\RouteServiceProvider;

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
        new RouteServiceProvider()->init();
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
