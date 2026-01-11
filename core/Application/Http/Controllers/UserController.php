<?php

namespace Core\Application\Http\Controllers;

use Core\Application\Event\UserCreated;
use Core\Application\Dao\UserDao;
use Core\Framework\Event\EventDispatcher\EventDispatcher;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

class UserController
{
    // this controller had a lot of logic in it, ideally all of this would be
    // inside of usecases and services - however, the intention of this project
    // is to build the backend framework tools that allow future developers to build
    // better code. this is just an example of a possible implementation of the tools
    // built in this repo

    public function __construct(
        private UserDao $userDao,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function list(Request $request): Response
    {
        $users = $this->userDao->get();

        return new Response(httpCode: 200, body: [
            'message' => 'users retrieved successfully',
            'data' => $users
        ]);
    }

    public function create(Request $request): Response
    {
        $request->validate([
            'name' => [
                'required' => true,
                'type' => 'string',
                'min' => 5,
                'max' => 255
            ],
            'email' => [
                'required' => true,
                'type' => 'string',
                'min' => 5,
                'max' => 255
            ],
            'password' => [
                'required' => true,
                'type' => 'string',
                'min' => 10,
                'max' => 64
            ]
        ]);

        $user = $this->userDao->create([
            'name' => $request->body['name'],
            'email' => $request->body['email'],
            'password' => $request->body['password']
        ]);

        $this->eventDispatcher->dispatch(new UserCreated(userId: $user['id']));

        return new Response(httpCode: 201, body: [
            'message' => 'user created successfully',
            'data' => $user
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        $user = $this->userDao->findBy([
            'id' => $id
        ]);

        return new Response(httpCode: 200, body: [
            'message' => 'user retrieved successfully',
            'data' => $user
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $request->validate([
            'name' => [
                'required' => true,
                'type' => 'string',
                'min' => 5,
                'max' => 255
            ],
            'email' => [
                'required' => true,
                'type' => 'string',
                'min' => 5,
                'max' => 255
            ]
        ]);

        $user = $this->userDao->update([
            'id' => $id
        ], [
            'name' => $request->body['name'],
            'email' => $request->body['email'],
            'password' => $request->body['password']
        ]);

        return new Response(httpCode: 200, body: [
            'message' => 'user updated successfully',
            'data' => $user
        ]);
    }

    public function delete(Request $request, string $id): Response
    {
        $this->userDao->delete([
            'id' => $id
        ]);

        return new Response(httpCode: 200, body: [
            'message' => 'user deleted successfully'
        ]);
    }
}