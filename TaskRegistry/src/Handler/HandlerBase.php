<?php
namespace Phalconeer\TaskRegistry\Handler;

use Phalconeer\Dto;
use Phalconeer\TaskRegistry as This;

abstract class HandlerBase implements This\TaskInterface
{
    const TASK_NAME = 'task-name-has-to-be-set';

    public function __construct(
        protected This\Data\ListenerConfig $config
    )
    {
        
    }

    public function config() : This\Data\ListenerConfig
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