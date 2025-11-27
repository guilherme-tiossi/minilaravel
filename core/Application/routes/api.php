<?php

use Core\Application\Http\Controllers\MessageController;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Router;
use Core\Application\Http\Middlewares\IpBlacklist;

# ver de tirar esse get instance
$router = Router::getInstance();

$router->addGroup(IpBlacklist::class, [
    [HttpMethod::GET, '/messages', MessageController::class, 'run']
]);
$router->addRoute(HttpMethod::GET, '/messages', MessageController::class, 'run');
$router->addRoute(HttpMethod::GET, '/messages/{uuid}', MessageController::class, 'show');
$router->addRoute(HttpMethod::GET, '/messages/{uuid}/resource/{resourceUuid}', MessageController::class, 'runs');
