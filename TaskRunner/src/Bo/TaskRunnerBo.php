<?php
namespace Phalconeer\TaskRunner\Bo;

use Phalconeer\Task;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskRegistry\Helper\TaskRegistryHelper as TRH;
use Phalconeer\TaskRunner as This;
use Phalcon\Config;

class TaskRunnerBo
{
    public function __construct(
        protected TaskRegistry\Bo\TaskRegistryBo $taskRegistryBo,
        protected Task\Bo\TaskBo $taskBo,
        protected int $limit = 60,
        protected Config\Config $config
    )
    {
        
    }

    public function listen()
    {
    /**
     * https://wiki.archlinux.org/title/Bash/Prompt_customization
     * https://stackoverflow.com/questions/34034730/how-to-enable-color-for-php-cli
     */
        $currentOffset = 0;
        echo '> trying ' . $this->limit . ' from ' . $this->taskBo->getSource() . PHP_EOL;
        while ($this->limit > 0)
        {
            $queueStatus = $this->taskBo->getQueueStatus($currentOffset);
            $statusText = null;
            if ($queueStatus->next()) {
                if ($taskResult = $this->executeTask($queueStatus->next())) {
                    $statusText = '|';
                    $currentOffset = 0;
                } else {
                    if (is_null($taskResult)) {
                        $statusText = 'X';
                        $currentOffset++;
                    } else {
                        $statusText = '>';
                        $currentOffset = 0;
                    }
                }
            } else {
                $currentOffset = 0;
            }

            if ($queueStatus->taskListLength() > 1) {
                echo $statusText ?? '.';
                usleep($this->config->get('busySleep', 200000));
            } else {
                echo $statusText ?? '-';
                usleep($this->config->get('lazySleep', 2000000));
            }
            $this->limit--;
        }
        echo ' <' . PHP_EOL;
    }

    public function executeTask(TaskRegistry\Data\TaskExecution $task) : ?bool
    {
        $task = $this->taskBo->claim($task);
        if (is_null($task)) {
            return null;
        }

        $taskName = $task->task();
        $module = $this->taskRegistryBo->getModule($taskName);
        if (is_null($module)) {
            $task = $this->taskBo->saveState(
                $task->setStatus(TRH::STATUS_FAILED)
            );
            throw new This\Exception\HandlerNotFoundException($taskName, This\Helper\ExceptionHelper::TASK__TASK_MODULE_NOT_LOADED);
        }

        // DONE has to be saved before evaluating the result to prevent duplicating the task
        $task = $this->taskBo->saveState(
            $task->setResult($module->handle($task->detail()))
                ->setStatus(TRH::STATUS_DONE)
        );

        if (!$task->result()?->success()) {
            $task = $this->taskBo->saveState(
                $task->setStatus(TRH::STATUS_FAILED)
                    ->incrementFailCount()
            );
            if ($task->failCount() < $this->config->get('retryCount', 5)) {
                $newTask = $this->taskRegistryBo->createTaskExecution(
                    $taskName,
                    $task->detail(),
                    new \ArrayObject([
                        'failCount'             => $task->failCount(),
                        'createdByTaskId'       => $task->id(),
                    ])
                );
                $this->taskBo->saveState($newTask);
            }
            return false;
        }

        $iterator = $task->result()?->followUpTasks()?->getIterator();
        while ($iterator->valid()) {
            $newTask = $iterator->current()->setCreatedBy($task->id());
            $this->taskBo->saveState($newTask);
            $iterator->next();
        }

        if (empty($this->taskRegistryBo->getConfig($taskName)->repeatInterval())) {
            return true;
        }

        $newTask = $this->taskRegistryBo->createTaskExecution(
            $taskName,
            $task->result()?->nextIterationDetail(),
            new \ArrayObject([
                'iterationId'           => $task->iterationId() + 1,
                'createdByTaskId'       => $task->id(),
            ])
        );
        $this->taskBo->saveState($newTask);
        return true;
    }

}