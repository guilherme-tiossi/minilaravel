<?php

namespace Core\Application\Http\Controllers;

use Core\Framework\Http\HttpResponse;
use Core\Framework\Http\Request;

// deve eventualmente estender de algum basecontroller
class MessageController
{
    public function run(Request $request): HttpResponse
    {
        return new HttpResponse(httpCode: 200, body: [
            'message' => 'controller consultado com sucesso',
            'json' => 'hehe!',
            'mensagem recebida de:' => $request->ip
        ]);
    }

    public function show(Request $request, string $uuid): HttpResponse
    {
        return new HttpResponse(httpCode: 200, body: [
            'message' => $uuid
        ]);
    }

    public function runs(Request $request, string $uuid, string $resourceUuid): HttpResponse
    {
        return new HttpResponse(httpCode: 200, body: [
            'message' => [
                'uuid' => $uuid,
                'resource' => $resourceUuid
            ]
        ]);
    }
}