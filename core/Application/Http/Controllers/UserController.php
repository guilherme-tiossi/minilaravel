<?php

namespace Core\Application\Http\Controllers;

use Core\Application\Model\User;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

// deve eventualmente estender de algum basecontroller
class UserController
{
    public function __construct(
        private User $userModel
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
        // adicionar alguma forma de validação
        // retornar algo de fato
        $user = $this->userModel->create([
            'name' => $request->body['name'],
            'email' => $request->body['email'],
            'password' => $request->body['password']
        ]);

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
        // adicionar alguma forma de validação
        // retornar algo de fato
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
        // achar antes de deletar
        $this->userModel->delete([
            'id' => $id
        ]);

        return new Response(httpCode: 200, body: [
            'message' => 'user deleted successfully'
        ]);
    }
}