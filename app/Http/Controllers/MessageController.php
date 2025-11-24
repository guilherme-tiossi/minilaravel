<?php

namespace App\Http\Controllers;

use Bootstrap\Http\HttpResponse;
use Bootstrap\Http\Request;

// deve eventualmente estender de algum basecontroller
// ver como passar query params por aki tb tipo /messages/id
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