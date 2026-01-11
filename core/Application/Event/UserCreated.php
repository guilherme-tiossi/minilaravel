<?php

namespace Core\Application\Event;

use Core\Framework\Event\Event;

class UserCreated extends Event
{
    public function __construct(
        public int $userId
    ) {
    }
}