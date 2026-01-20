<?php

namespace Core\Framework\Root;

require __DIR__ . '/../public/helpers.php';

use Core\Framework\Http\Router;
use Core\Framework\Providers\RouteServiceProvider;
use Core\Framework\Queue\QueueWorker;

class Miniartisan 
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
        echo "php miniartisan routes # lists all available routes\n";
        echo "php miniartisan help # lists available commands\n";
        echo "php miniartisan queue:work {queueName} # starts specific queue. replace {queueName} with the actual queue name\n";
        exit();
    }

    public function routes(): void
    {
        new RouteServiceProvider()->register();
        echo "List of routes:\n";
        Router::routes();
        exit();
    }

    public function queue(string $queueName): void
    {
        $container = new Container();
        new Application($container);
        $worker = $container->make(QueueWorker::class);
        $worker->work($queueName, $container);
    }

    public function error(): void
    {
        echo "FATAL MINIARTISAN ERROR:\n";
        echo "Unavailable command!\n";
        exit();
    }
}
