<?php

namespace Core\Application\Event;

use Core\Framework\Event\Event;

class ProposalCreated extends Event
{
    public function __construct(
        public int $proposalId
    ) {
    }
}