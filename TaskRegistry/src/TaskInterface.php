<?php
namespace Phalconeer\TaskRegistry;

use Phalconeer\Dto;
use Phalconeer\TaskRegistry as This;

interface TaskInterface
{
    public function config() : This\Data\ListenerConfig;

    public function handle(Dto\ArrayObjectExporterInterface $detail = null) : This\Data\TaskResult;

    public function isRunningCondition(This\Data\TaskExecution $task = null) : ?array;

    public function taskName() : string;

    public function validateParameters(array $parameters = null) : ?Dto\ArrayObjectExporterInterface;
}