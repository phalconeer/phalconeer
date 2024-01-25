<?php
namespace Phalconeer\TaskAdmin\Bo;

use Phalconeer\TaskAdmin as This;
use Phalcon\Config;
use Phalconeer\Condition;
use Phalconeer\Dao;
use Phalconeer\Dto;
use Phalconeer\ElasticAdapter;
use Phalconeer\Task;

class TaskAdminBo
{
    public function __construct(
        protected Dao\DaoReadAndWriteInterface $dao,
        protected Config\Config $config,
        protected Task\Bo\TaskBo $taskBo
    )
    {
    }

    public function getTasks(
        array $whereConditions,
        int $limit = 20,
        int $offset = 0,
        string $orderString = ''
    ) : ?Task\Data\TaskExecutionCollection
    {
        $data = $this->dao->getRecords(
            $whereConditions,
            $limit,
            $offset,
            $orderString
        )?->offsetGet(ElasticAdapter\Helper\ElasticResponseHelper::NODE_HITS);

        return (is_null($data))
            ? null
            : new Task\Data\TaskExecutionCollection($data);
    }

    public function getTaskCount(array $whereConditions)
    {
        return $this->dao->getCount($whereConditions);
    }

    public function cleanErroredTasks()
    {
        $erroredTasks = $this->getTasks([
            'status'            => Task\Helper\TaskHelper::STATUS_PROCESSING,
            'expectedRunTime'   => [
                'operator'          => Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL,
                'value'             => (new \DateTime())
                    ->setTimestamp(time() - $this->config->get('cleanErroredTasksAfter', 180))
            ]
        ]);
        if ($erroredTasks->count() === 0) {
            return 0;
        }
        $iterator = $erroredTasks->getIterator();
        while ($iterator->valid()) {
            $task = $iterator->current()->setStatus(Task\Helper\TaskHelper::STATUS_ERRORED);
            $this->dao->save($task);
            $iterator->next();
        }
        return $erroredTasks->count();
    }

    public function saveTask(Task\Data\TaskExecution $task) : ?Task\Data\TaskExecution
    {
        $module = $this->taskBo->getModule($task->task());
        if (is_null($module)) {
            throw new This\Exception\InvalidTaskException($task->task(), This\Helper\ExceptionHelper::CREATE_TASK__CONFIGURATION_NOT_FOUND);
        }

        if (!is_null($isRunningCondition = $module->isRunningCondition($task))) {
            $currentTaskCount = $this->getTaskCount($isRunningCondition);
            if ($currentTaskCount > 0) {
                throw new This\Exception\TaskAlreadyStartedException($task->task(), This\Helper\ExceptionHelper::CREATE_TASK__TASK_ALREADY_STARTED);
            }
        }
        return $this->dao->save($task);
    }

    public function deleteTaskRun(Task\Data\TaskExecution $task)
    {
        return $this->dao->save($task->setStatus(Task\Helper\TaskHelper::STATUS_CANCELLED));
    }
}