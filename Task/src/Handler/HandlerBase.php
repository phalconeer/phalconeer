<?php
namespace Phalconeer\Task\Handler;

use Phalconeer\Dto;
use Phalconeer\Task as This;
use Phalcon\Config;

abstract class HandlerBase implements This\TaskInterface
{
    const TASK_NAME = 'task-name-has-to-be-set';

    protected Config\Config $config;

    public function config() : Config\Config
    {
        return $this->config;
    }

    public function isRunningCondition(This\Data\TaskExecution $task = null) : ?array
    {
        return null;
    }

    public function taskName(): string
    {
        return static::TASK_NAME;
    }

    public function validateParameters(array $parameters = null) : ?Dto\ArrayObjectExporterInterface
    {
        return null;
    }
}