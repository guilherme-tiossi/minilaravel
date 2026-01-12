<?php

namespace Core\Application\Listener;

use Core\Application\Dao\ProposalDao;
use Core\Application\Event\ProposalCreated;
use Core\Shared\Exceptions\AppException;
use Throwable;

class RegisterExternalProposal
{
    public $queue = 'proposals';
    public $maxTries = 10;

    public function __construct(
        private ProposalDao $proposalDao,
    ) {
    }

    public function run(ProposalCreated $event): void
    {
        // MUDAR PARA ALGUMA FACADE INTERNA DO MINILARAVEL
        $ch = curl_init('https://util.devi.tools/api/v2/authorize');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        

        if (json_decode($response)->status == 'success') {
            $this->proposalDao->update([
                'id' => $event->proposalId
            ], [
                'status' => 'finished'
            ]);
            // lançar atualização
        } else {
            throw new AppException(503, 'resource not ready');
        }
    }

    public function failed(ProposalCreated $event, ?Throwable $e = null): void
    {
    }
}