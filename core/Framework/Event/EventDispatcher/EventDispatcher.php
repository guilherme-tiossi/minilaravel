<?php

namespace Core\Framework\Event\EventDispatcher;

use Core\Framework\Event\Event;

class EventDispatcher
{
    public function __construct(
        private array $listeners
    ) {
    }

    public function dispatch(Event $event): void
    {
        sleep(7);
        foreach ($this->listeners[$event::class] as $listener) {
            if (isset($listener->queue) && $listener->queue) {
                // $this->jobModel->create([
                //     'runner_class' => $runnerClass,
                //     'event_class' => get_class($event),
                //     'status' => 'pending',
                //     'worker' => null,
                //     'queue' => $runnerClass->queue,
                //     'max_tries' => $runnerClass->max_tries ?? 1
                // ]);

                return;
            }

            $listener->run($event);
        }
    }
}