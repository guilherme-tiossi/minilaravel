<?php

namespace Core\Application\Http\Controllers;

use Core\Application\Event\UserCreated;
use Core\Application\Model\User;
use Core\Framework\Event\EventDispatcher\EventDispatcher;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

class UserController
{
    public function __construct(
        private User $userModel,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function list(Request $request): Response
    {
        $users = $this->userModel->get();

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

        $user = $this->userModel->create([
            'name' => $request->body['name'],
            'email' => $request->body['email'],
            'password' => $request->body['password']
        ]);

        $this->eventDispatcher->dispatch(new UserCreated($user['id']));

        return new Response(httpCode: 201, body: [
            'message' => 'user created successfully',
            'data' => $user
        ]);
    }

    public function show(Request $request, string $id): Response
    {
        $user = $this->userModel->findBy([
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

        $user = $this->userModel->update([
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
        $this->userModel->delete([
            'id' => $id
        ]);

        return new Response(httpCode: 200, body: [
            'message' => 'user deleted successfully'
        ]);
    }
}