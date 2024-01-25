<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;
use Phalconeer\Task as This;

class TaskStat extends Data\ImmutableData
{
    use Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected int $count;
    
    protected string $key;

    protected int $order;

    protected This\Data\TaskStatusStatCollection $statusDetailed;
}