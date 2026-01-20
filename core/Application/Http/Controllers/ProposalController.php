<?php

namespace Core\Application\Http\Controllers;

use Core\Application\Event\ProposalCreated;
use Core\Application\Dao\ProposalDao;
use Core\Framework\Event\EventDispatcher\EventDispatcher;
use Core\Framework\Http\ValueObjects\Response;
use Core\Framework\Http\ValueObjects\Request;

class ProposalController
{
    // this controller has a lot of logic in it, ideally all of this would be
    // inside of usecases and services - however, the intention of this project
    // is to build the backend framework tools that allow future developers to build
    // better code. this is just an example of a possible implementation of the tools
    // built in this repo

    public function __construct(
        private ProposalDao $proposalDao,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function create(Request $request): Response
    {
        $request->validate([
            'cpf' => [
                'required' => true,
                'type' => 'string',
                'min' => 11,
                'max' => 11
            ],
            'name' => [
                'required' => true,
                'type' => 'string',
                'min' => 5,
                'max' => 255
            ],
            'birth_date' => [
                'required' => true,
                'type' => 'string',
                'min' => 10,
                'max' => 10
            ],
            'proposal_value' => [
                'required' => true,
                'type' => 'float',
                'min' => 10,
                'max' => 9999
            ],
            'pix_key' => [
                'required' => true,
                'type' => 'string',
                'min' => 10,
                'max' => 255
            ]
        ]);

        $proposal = $this->proposalDao->create([
            'cpf' => $request->body['cpf'],
            'name' => $request->body['name'],
            'birth_date' => $request->body['birth_date'],
            'proposal_value' => $request->body['proposal_value'],
            'pix_key' => $request->body['pix_key'],
            'status' => 'pending'
        ]);

        $this->eventDispatcher->dispatch(new ProposalCreated(proposalId: $proposal['id']));

        return new Response(httpCode: 201, body: [
            'message' => 'proposal created successfully',
            'data' => $proposal
        ]);
    }
}