<?php
namespace Phalconeer\Task;

use Phalconeer\Dao;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Task as This;

interface TaskDaoInterface extends Dao\DaoReadAndWriteInterface
{
    public function claim(Data\DataInterface & Dto\ArrayObjectExporterInterface $data): bool;

    public function getQueueStatus($offset = 0): This\Data\QueueStatus;
}