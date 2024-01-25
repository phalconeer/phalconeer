<?php
namespace Phalconeer\TaskTest\Bo;

use Phalconeer\Dto;
use Phalconeer\Task;
use Phalconeer\TaskTest as This;

class TaskTestBo extends Task\Handler\HandlerBase
{
    const TASK_NAME = This\Factory::MODULE_NAME;

    public function handle(Dto\ArrayObjectExporterInterface | This\Data\TaskTest $detail = null) : Task\Data\TaskResult
    {
        echo PHP_EOL . 'TASK IS NOW BEING RUN!: ' . $detail->message() . PHP_EOL . PHP_EOL;
        return Task\Data\TaskResult::fromArray([
            'result'    => 'Task is complete',
            'success'   => true
        ]);
    }

    public function isRunningCondition(Task\Data\TaskExecution $task = null) : ?array
    {
        return [
            'task'      => static::TASK_NAME,
            'status'    => [
                Task\Helper\TaskHelper::STATUS_NEW,
                Task\Helper\TaskHelper::STATUS_PROCESSING,
            ]
        ];
    }
}