<?php

namespace Core\Application\Listener;

use Core\Application\Event\UserCreated;
use Core\Application\Dao\UserDao;
use Core\Application\Dao\LogDao;
use Throwable;

class SendAccountConfirmationEmail
{
    public $queue = 'email';
    public $maxTries = 3;

    public function __construct(
        private UserDao $userDao,
        private LogDao $logDao
    ) {
    }

    public function run(UserCreated $event): void
    {
        // this class exists only to serve as an example of the functionality of the
        // event/listener implementation of this miniframwork, so for now i will not
        // implement a real email sender.

        $this->userDao->update([
            'id' => $event->userId
        ], [
            'received_confirmation_email' => true
        ]);
    }

    public function failed(UserCreated $event, ?Throwable $e = null): void
    {
        $this->logDao->create([
            'context'        => 'SendAccountConfirmationEmail',
            'error_message'  => $e?->getMessage(),
            'error_code'     => $e?->getCode(),
            'exception'      => $e ? get_class($e) : null,
            'stack_trace'    => $e?->getTraceAsString()
        ]);
    }
}