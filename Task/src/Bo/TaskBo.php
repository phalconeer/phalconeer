<?php
namespace Phalconeer\Task\Bo;

use Phalconeer\Task as This;
use Phalconeer\TaskRegistry;

class TaskBo
{
    public function __construct(
        protected TaskRegistry\TaskDaoInterface $dao,
    )
    {
    }

    public function getSource() : string
    {
        return $this->dao->indentity();
    }

    public function claim(TaskRegistry\Data\TaskExecution $task) : ?TaskRegistry\Data\TaskExecution
    {
        $task = $task->setStatus(TaskRegistry\Helper\TaskRegistryHelper::STATUS_PROCESSING)
                ->setActualRunTime()
                ->setExecutedOn();
        return ($this->dao->claim($task))
            ? $task
            : null;
    }

    public function saveState(TaskRegistry\Data\TaskExecution $task) : TaskRegistry\Data\TaskExecution
    {
        return $this->dao->save($task);
    }

    public function getQueueStatus($offset = 0) : TaskRegistry\Data\QueueStatus
    {
        return $this->dao->getQueueStatus($offset);
    }

    public function getTasks(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?TaskRegistry\Data\TaskExecutionCollection
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
            : new TaskRegistry\Data\TaskExecutionCollection($data->offsetGet('hits'));
    }
}