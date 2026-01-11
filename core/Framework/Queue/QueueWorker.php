<?php

namespace Core\Framework\Queue;

use Throwable;
use Core\Application\Dao\JobDao;
use Core\Framework\Root\Container;
use Core\Infrastructure\Database\DatabaseProvider;
use Exception;

class QueueWorker
{
    public function __construct(
        private JobDao $jobDao,
        private DatabaseProvider $databaseProvider
    ) {
    }

    // mudar, container não pode ser usado aqui
    public function work(string $queueName, Container $container)
    {
        echo "running jobs from $queueName :D \n";
        echo "worker:" . getmypid() . "\n";

        while (true) {
            $this->databaseProvider->initTransaction();

            // mudar para suportar um optionsDto
            $jobs = $this->jobDao->get([
                'queue' => $queueName,
                'status' => 'pending',
                'worker' => null
            ], [
                'limit' => 4,
                'for_update' => true,
                'order' => [
                    'column' => 'id',
                    'orientation' => 'asc'
                ]
            ]);

            if (empty($jobs)) {
                $this->databaseProvider->rollbackTransaction();
                sleep(5);
                continue;
            }

            $jobIds = array_column($jobs, 'id');
            $this->jobDao->updateBatch($jobIds,
            [
                'status' => 'reserved',
                'worker' => getmypid()
            ]);
            $this->databaseProvider->commitTransaction(); // commit após update

            // sleep implementado apenas para testes, remover futuramente antes de lançar o projeto
            sleep(5);
            $this->processJobs($jobs, $container);
        }
    }

    private function processJobs(array $jobs, Container $container)
    {
        foreach ($jobs as $job) {
            $jobName = $this->getJobName($job);
            echo "currently processing - $jobName - ".$job['id']."\n";
            $currentTries = $job['tries'] + 1;
            $this->databaseProvider->initTransaction();
            try {
                $jobRunner = $container->make($job['runner_class']);
                $jobEvent = new $job['event_class'](...json_decode($job['event_params'], true));
                $jobRunner->run($jobEvent);
                $this->jobDao->update([
                    'id' => $job['id']
                ], [
                    'status' => 'finished'
                ]);
                $this->databaseProvider->commitTransaction();
                echo "finished successfully - $jobName\n";
            } catch (Throwable $e) {
                echo "failed! - $jobName\n";
                $this->databaseProvider->rollbackTransaction();

                if ($job['max_tries'] > $currentTries) {
                    $this->jobDao->update([
                        'id' => $job['id']
                    ], [
                        'status' => 'pending',
                        'worker' => null,
                        'tries' => $currentTries
                    ]);
                } else {
                    $this->jobDao->update([
                        'id' => $job['id']
                    ], [
                        'status' => 'failed',
                        'tries' => $currentTries
                    ]);
                }
            }
        }
    }

    private function getJobName(array $job): string
    {
        $parts = explode("\\", $job['runner_class']);
        return end($parts);
    }
}