<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;

class TaskStatusStatCollection extends Data\ImmutableCollection
{
    protected $collectionType = TaskStatusStat::class;
}