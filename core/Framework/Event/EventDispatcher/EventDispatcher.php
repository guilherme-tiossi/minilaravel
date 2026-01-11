<?php

namespace Core\Framework\Event\EventDispatcher;

use Core\Application\Dao\JobDao;
use Core\Framework\Event\Event;
use ReflectionClass;

class EventDispatcher
{
    public function __construct(
        private array $listeners,
        private JobDao $jobDao
    ) {
    }

    public function dispatch(Event $event): void
    {
        foreach ($this->listeners[$event::class] as $listener) {
            if (isset($listener->queue) && $listener->queue) {
                $eventParams = $this->extractEventParams($event);

                $this->jobDao->create([
                    'runner_class' => get_class($listener),
                    'event_class' => get_class($event),
                    'event_params' => json_encode($eventParams),
                    'status' => 'pending',
                    'worker' => null,
                    'queue' => $listener->queue,
                    'max_tries' => $runnerClass->maxTries ?? 1
                ]);

                return;
            }

            $listener->run($event);
        }
    }

    private function extractEventParams(Event $event): array
    {
        $reflection = new ReflectionClass($event);
        $params = [];
        if ($constructor = $reflection->getConstructor()) {
            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                $params[$name] = $event->$name;
            }
        }

        return $params;
    }
}