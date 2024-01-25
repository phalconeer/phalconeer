<?php
namespace Phalconeer\TaskRunner\Bo;

use Phalconeer\Task;
use Phalcon\Config;

class TaskRunnerBo
{
    public function __construct(
        protected Task\Bo\TaskBo $taskBo,
        protected int $limit = 60,
        protected Config\Config $config
    )
    {
        
    }

    public function listen()
    {
        $currentOffset = 0;
        echo '> ' . $this->limit . ' left' . PHP_EOL;
        while ($this->limit > 0)
        {
            $queueStatus = $this->taskBo->getQueueStatus($currentOffset);
            $statusText = null;
            if ($queueStatus->next()) {
                if ($taskResult = $this->taskBo->executeTask($queueStatus->task())) {
                    $statusText = '✅';
                    $currentOffset = 0;
                } else {
                    if (is_null($taskResult)) {
                        $statusText = '⏭️';
                        $currentOffset++;
                    } else {
                        $statusText = '❌';
                        $currentOffset = 0;
                    }
                }
            } else {
                $currentOffset = 0;
            }


            if ($queueStatus->taskListLength() > 1) {
                echo $statusText ?? 'o';
                usleep($this->config->get('busySleep', 200000));
            } else {
                echo $statusText ?? '.';
                usleep($this->config->get('lazySleep', 2000000));
            }
            $this->limit--;
        }
        echo ' <' . PHP_EOL;
    }

}