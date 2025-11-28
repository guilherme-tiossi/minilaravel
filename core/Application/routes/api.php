<?php

use Core\Application\Http\Controllers\MessageController;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Router;
use Core\Application\Http\Middlewares\IpBlacklist;

Router::group(IpBlacklist::class, [
    [HttpMethod::GET, '/messages', MessageController::class, 'run']
]);
Router::get('/messages', MessageController::class, 'run');
Router::get('/messages/{uuid}', MessageController::class, 'show');
Router::get('/messages/{uuid}/resource/{resourceUuid}', MessageController::class, 'runs');
