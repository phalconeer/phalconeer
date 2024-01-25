<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;

class TaskStatusStat extends Data\ImmutableData
{
    use Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected int $count;
    
    protected string $status;
}