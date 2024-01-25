<?php
namespace Phalconeer\TaskTest\Data;

use Phalconeer\Data;
use Phalconeer\Task;

class TaskTestWithTraits extends Task\Data\TaskParameters
{
    use Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;
        
    protected string $message;
}