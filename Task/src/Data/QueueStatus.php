<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Task as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class QueueStatus extends Data\ImmutableData
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected ?This\Data\TaskExecution $next;

    protected int $taskListLength;
}