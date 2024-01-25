<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;

class TaskExecutionCollection extends Data\ImmutableCollection
{
    protected string $collectionType = TaskExecution::class;
}