<?php

use App\Http\Controllers\MessageController;
use Bootstrap\Http\Enums\HttpMethod;
use Bootstrap\Http\Router;
use App\Http\Middlewares\IpBlacklist;

$router = Router::getInstance();

$router->addGroup(IpBlacklist::class, [
    [HttpMethod::GET, '/messages', MessageController::class, 'run']
]);
$router->addRoute(HttpMethod::GET, '/messages', MessageController::class, 'run');
$router->addRoute(HttpMethod::GET, '/messages/{uuid}', MessageController::class, 'show');
$router->addRoute(HttpMethod::GET, '/messages/{uuid}/resource/{resourceUuid}', MessageController::class, 'runs');
