<?php

namespace Core\Application\Listener;

use Core\Application\Dao\ProposalDao;
use Core\Application\Event\ProposalCreated;
use Core\Framework\Http\Services\HttpClient\HttpClient;
use Core\Shared\Exceptions\AppException;
use Throwable;

class RegisterExternalProposal
{
    public $queue = 'proposals';
    public $maxTries = 10;

    public function __construct(
        private ProposalDao $proposalDao,
        private HttpClient $httpClient
    ) {
    }

    public function run(ProposalCreated $event): void
    {
        $response = $this->httpClient->get('https://util.devi.tools/api/v2/authorize');
        $data = $response->json();

        if ($data['status'] == 'success') {
            $this->proposalDao->update([
                'id' => $event->proposalId
            ], [
                'status' => 'finished'
            ]);
        } else {
            throw new AppException(503, 'resource not ready');
        }
    }

    public function failed(ProposalCreated $event, ?Throwable $e = null): void
    {
    }
}