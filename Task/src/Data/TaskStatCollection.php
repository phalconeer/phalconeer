<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;

class TaskStatCollection extends Data\ImmutableCollection
{
    protected $collectionType = TaskStat::class;
}