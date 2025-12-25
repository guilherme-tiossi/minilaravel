<?php

use Core\Application\Http\Controllers\MessageController;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Router;
use Core\Application\Http\Middlewares\IpBlacklist;

Router::group(IpBlacklist::class, [
    [HttpMethod::GET, '/messages', MessageController::class, 'run']
]);
Router::addRoute(HttpMethod::GET, '/messages/{uuid}', MessageController::class, 'show');
Router::addRoute(HttpMethod::GET,'/messages/{uuid}/resource/{resourceUuid}', MessageController::class, 'runs');
