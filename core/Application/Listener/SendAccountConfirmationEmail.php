<?php

namespace Core\Application\Listener;

use Core\Application\Event\UserCreated;
use Core\Application\Dao\UserDao;
use Throwable;

class SendAccountConfirmationEmail
{
    public $queue = 'teste';
    public $maxTries = 9;

    public function __construct(
        private UserDao $userDao
    ) {
    }

    public function run(UserCreated $event): void
    {
        // this class exists only to serve as an example of the functionality of the
        // event/service implementation of this miniframwork, so for now i will not
        // implement a real email sender.

        $this->userDao->update([
            'id' => $event->userId
        ], [
            'received_confirmation_email' => true
        ]);
    }

    public function failed(UserCreated $event, ?Throwable $e = null): void
    {
        // log something in the future  
    }
}