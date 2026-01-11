<?php

namespace Core\Application\Http\Controllers;

use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

class MessageController
{
    public function __construct() 
    {
    }

    public function run(Request $request): Response
    {
        return new Response(httpCode: 200, body: [
            'message' => 'controller consultado com sucesso',
            'json' => 'hehe!',
            'mensagem recebida de:' => $request->ip
        ]);
    }

    public function show(Request $request, string $uuid): Response
    {
        return new Response(httpCode: 200, body: [
            'message' => $uuid
        ]);
    }

    public function runs(Request $request, string $uuid, string $resourceUuid): Response
    {
        return new Response(httpCode: 200, body: [
            'message' => [
                'uuid' => $uuid,
                'resource' => $resourceUuid
            ]
        ]);
    }
}
