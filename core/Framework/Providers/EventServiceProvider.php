<?php

namespace Core\Framework\Providers;

use Core\Application\Event\UserCreated;
use Core\Application\Listener\SendAccountConfirmationEmail;
use Core\Application\Model\Job;
use Core\Framework\Event\EventDispatcher\EventDispatcher;
use Core\Framework\Root\Container;

class EventServiceProvider implements Provider
{
    private array $listen = [
        UserCreated::class => [
            SendAccountConfirmationEmail::class
        ]
    ]; 

    public function register(?Container &$container = null): void
    {
        $container->bind(EventDispatcher::class, function () use ($container) {
            $listeners = [];
            
            foreach ($this->listen as $event => $classes) {
                foreach ($classes as $class) {
                    $listeners[$event][] = $container->make($class);
                }
            }
            
            return new EventDispatcher($listeners, $container->make(Job::class));
        });
    }
}
