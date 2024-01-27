<?php
namespace Phalconeer\TaskTest\Bo;

use Phalconeer\Dto;
use Phalconeer\TaskRegistry;
use Phalconeer\TaskTest as This;

class TaskTestBo extends TaskRegistry\Handler\HandlerBase
{
    const TASK_NAME = This\Factory::MODULE_NAME;

    public function validateParameters(array $parameters = null) : ?Dto\ArrayObjectExporterInterface
    {
        $className = $this->config->parameterClass() ?? This\Data\TaskTest::class;
        return $className::fromArray($parameters);
    }

    public function handle(Dto\ArrayObjectExporterInterface $detail = null) : TaskRegistry\Data\TaskResult
    {
        /**
         * @var This\Data\TaskTest $detail
         */
        echo PHP_EOL . 'TASK IS NOW BEING RUN!: ' . $detail?->message() . PHP_EOL . PHP_EOL;
        return TaskRegistry\Data\TaskResult::fromArray([
            'nextIterationDetail'   => $this->validateParameters([
                'message'   => $detail?->message() . '.',
            ]),
            'result'    => 'Task is complete',
            'success'   => true
        ]);
    }

    public function isRunningCondition(TaskRegistry\Data\TaskExecution $task = null) : ?array
    {
        return [
            'task'      => static::TASK_NAME,
            'status'    => [
                TaskRegistry\Helper\TaskRegistryHelper::STATUS_NEW,
                TaskRegistry\Helper\TaskRegistryHelper::STATUS_PROCESSING,
            ]
        ];
    }
}