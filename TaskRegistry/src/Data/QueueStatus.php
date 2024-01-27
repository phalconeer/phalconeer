<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\TaskRegistry as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class QueueStatus extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $loadTransformers = [
        [Dto\Transformer\ArrayLoader::class, Dto\Transformer\ArrayLoader::AUTO_CONVERT_METHOD]
    ];

    protected ?This\Data\TaskExecution $next;

    protected int $taskListLength;
}