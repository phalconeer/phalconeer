<?php
namespace Phalconeer\Task\Bo;

use Phalconeer\Task as This;
use Phalcon\Config;

class TaskBo
{
    public function __construct(
        protected This\TaskDaoInterface $dao,
        protected Config\Config $config
    )
    {
    }

    public function getModule(string $taskName) : ?This\TaskInterface
    {
    echo \Phalconeer\Dev\TVarDumper::dump($this->config);die();
        $module = $this->config?->get(This\Helper\TaskHelper::CONFIG_HANDLER)
            ?->get($taskName)
            ?->get('moduleInstance');
        return ($module instanceof This\TaskInterface) ? $module : null;
    }

    protected function claim(This\Data\TaskExecution $task) : ?This\Data\TaskExecution
    {
        $task = $task->setStatus(This\Helper\TaskHelper::STATUS_PROCESSING)
                ->setActualRunTime()
                ->setExecutedOn();
        return ($this->dao->claim($task))
            ? $task
            : null;
    }

    protected function saveState(This\Data\TaskExecution $task) : This\Data\TaskExecution
    {
        return $this->dao->save($task);
    }

    public function executeTask(This\Data\TaskExecution $task) : ?bool
    {
        $task = $this->claim($task);
        if (is_null($task)) {
            return null;
        }

        $taskName = $task->task();
        $module = $this->getModule($taskName);
        if (is_null($module)) {
            $task = $this->saveState(
                $task->setStatus(This\Helper\TaskHelper::STATUS_FAILED)
            );
            throw new This\Exception\HandlerNotFoundException($taskName, This\Helper\ExceptionHelper::TASK__TASK_MODULE_NOT_LOADED);
        }
        $config = $this->config?->get(This\Helper\TaskHelper::CONFIG_HANDLER)?->get($taskName);

        $detailObject = $task->detailObject();
        // DONE has to be saved before evaluating the result to prevent duplicating the task
        $task = $this->saveState(
            $task->setResult($module->handle($detailObject))
                ->setStatus(This\Helper\TaskHelper::STATUS_DONE)
        );

        if (!$task->resultObject()->success()) {
            $task = $this->saveState(
                $task->setStatus(This\Helper\TaskHelper::STATUS_FAILED)
                    ->incrementFailCount()
            );
            if ($task->failCount() < $this->config->get('retryCount', 5)) {
                $newTask = This\Helper\TaskHelper::createTaskExecution(
                    $taskName,
                    $config,
                    $detailObject,
                    new \ArrayObject([
                        'failCount'             => $task->failCount(),
                        'createdByTaskId'       => $task->id(),
                    ])
                );
                $this->dao->save($newTask);
            }
            return false;
        }

        $iterator = $task->resultObject()->followUpTasksCollection()->getIterator();
        while ($iterator->valid()) {
            $newTask = $iterator->current()->setCreatedBy($task->id());
            $this->dao->save($newTask);
            $iterator->next();
        }

        if (empty($config->repeatInterval)) {
            return true;
        }

        $newTask = This\Helper\TaskHelper::createTaskExecution(
            $taskName,
            $config,
            $task->resultObject()->nextIterationDetailObject(),
            new \ArrayObject([
                'iterationId'           => $task->iterationId() + 1,
                'createdByTaskId'       => $task->id(),
            ])
        );
        $this->dao->save($newTask);
        return true;
    }

    public function getQueueStatus($offset = 0) : This\Data\QueueStatus
    {
        return $this->dao->getQueueStatus($offset);
    }

    public function getTasks(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?This\Data\TaskExecutionCollection
    {
        $data = $this->dao->getRecords(
            $whereConditions,
            $limit,
            $offset,
            $orderString
        );

        return (is_null($data)
            || !$data->offsetExists('hits'))
            ? null
            : new This\Data\TaskExecutionCollection($data->offsetGet('hits'));
    }
}