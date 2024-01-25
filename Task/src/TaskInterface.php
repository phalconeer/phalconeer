<?php
namespace Phalconeer\Task;

use Phalconeer\Dto;
use Phalconeer\Task as This;
use Phalcon\Config;

interface TaskInterface
{
    public function config() : Config\Config;

    public function handle(Dto\ArrayObjectExporterInterface $detail = null) : This\Data\TaskResult;

    public function isRunningCondition(This\Data\TaskExecution $task = null) : ?array;

    public function taskName() : string;

    public function validateParameters(array $parameters = null) : ?Dto\ArrayObjectExporterInterface;
}