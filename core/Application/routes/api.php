<?php

use Core\Application\Http\Controllers\MessageController;
use Core\Application\Http\Controllers\ProposalController;
use Core\Application\Http\Controllers\UserController;
use Core\Framework\Http\Enums\HttpMethod;
use Core\Framework\Http\Router;
use Core\Application\Http\Middlewares\IpBlacklist;


Router::addRoute(HttpMethod::POST,'/proposals', ProposalController::class, 'create');

Router::group(IpBlacklist::class, [
    [HttpMethod::GET, '/users', UserController::class, 'list'],
    [HttpMethod::POST, '/users', UserController::class, 'create'],
    [HttpMethod::GET, '/users/{id}', UserController::class, 'show'],
    [HttpMethod::PATCH, '/users/{id}', UserController::class, 'update'],
    [HttpMethod::DELETE, '/users/{id}', UserController::class, 'delete']
]);

Router::group(IpBlacklist::class, [
    [HttpMethod::GET, '/messages', MessageController::class, 'run']
]);
Router::addRoute(HttpMethod::GET, '/messages/{uuid}', MessageController::class, 'show');
Router::addRoute(HttpMethod::GET,'/messages/{uuid}/resource/{resourceUuid}', MessageController::class, 'runs');
