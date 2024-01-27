<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\Dto;

class TaskExecutionCollection extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\ArrayLoader;

    protected string $collectionType = TaskExecution::class;
}