<?php

namespace Core\Framework\Queue;

use Throwable;
use Core\Application\Model\Job;
use Core\Framework\Root\Container;
use Core\Infrastructure\Database\DatabaseProvider;

class QueueWorker
{
    public function __construct(
        private Job $jobModel,
        private DatabaseProvider $databaseProvider
    ) {
    }

    // mudar, container não pode ser usado aqui
    public function work(string $queueName, Container $container)
    {
        while (true) {
            $this->databaseProvider->initTransaction();
            $jobs = $this->jobModel->get([
                'queue' => $queueName,
                'status' => 'pending',
                'worker' => null
            ]);

            if (empty($jobs)) {
                $this->databaseProvider->rollbackTransaction();
                sleep(5);
                continue;
            }
            sleep(15);
            $jobIds = array_column($jobs, 'id');
            $this->jobModel->updateBatch($jobIds,
            [
                'status' => 'reserved',
                'worker' => getmypid()
            ]);
            $this->databaseProvider->commitTransaction();

            $this->processJobs($jobs, $container);
        }
    }

    private function processJobs(array $jobs, Container $container)
    {
        foreach ($jobs as $job) {
            $currentTries = $job['tries'] + 1;
            $this->databaseProvider->initTransaction();
            try {
                $jobRunner = $container->make($job['runner_class']);
                $jobEvent = new $job['event_class'](...json_decode($job['event_params'], true));
                $jobRunner->run($jobEvent);
                $this->jobModel->update([
                    'id' => $job['id']
                ], [
                    'status' => 'finished'
                ]);
                $this->databaseProvider->commitTransaction();
            } catch (Throwable $e) {
                $this->databaseProvider->rollbackTransaction();

                dd($e);
                if ($job['max_tries'] > $currentTries) {
                    $jobs = $this->jobModel->update([
                        'id' => $job['id']
                    ], [
                        'status' => 'pending',
                        'worker' => null,
                        'tries' => $currentTries
                    ]);
                } else {
                    $jobs = $this->jobModel->update([
                        'id' => $job['id']
                    ], [
                        'status' => 'failed',
                        'tries' => $currentTries
                    ]);
                }
            }
        }
    }
}