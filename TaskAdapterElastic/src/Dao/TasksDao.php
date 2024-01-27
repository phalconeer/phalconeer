<?php
namespace Phalconeer\TaskAdapterElastic\Dao;

use Phalconeer\Condition;
use Phalconeer\Dao;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\ElasticAdapter;
use Phalconeer\ElasticAdapter\Helper\ElasticResponseHelper as ERH;
use Phalconeer\TaskRegistry;

class TasksDao extends ElasticAdapter\Dao\ElasticDaoBase implements TaskRegistry\TaskDaoInterface
{
    public string $indexName = 'tasklog-*';

    public function claim(Data\DataInterface & Dto\ArrayObjectExporterInterface $data): bool
    {
        try {
            $data = $this->save(
                $data,
                false,
                Dao\Helper\DaoHelper::INSERT_MODE_NORMAL,
                true
            );
        } catch (ElasticAdapter\Exception\ElasticVersionConflictException $ex) {
            return false;
        }

        return true;
    }

    public function getQueueStatus($offset = 0): TaskRegistry\Data\QueueStatus
    {
        $conditions = [
            'status'        => TaskRegistry\Helper\TaskRegistryHelper::STATUS_NEW,
            'expectedRunTime'   => [
                'operator'          => Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL,
                'value'             => new \DateTime()
            ]
        ];

        $nextTask = $this->getRecords(
            $conditions,
            1,
            $offset,
            '-priority,expectedRunTime',
            true
        );
    
        $task = ($nextTask->offsetGet(ERH::NODE_HITS_TOTAL) > $offset)
            ? $nextTask->offsetGet(ERH::NODE_HITS)[0]
            : null;

        return TaskRegistry\Data\QueueStatus::fromArray([
            'next'                  => $task,
            'taskListLength'        => $this->getCount($conditions),
        ]);
    }

    public function indentity(): string
    {
        return 'ElasticSearch ' . $this->indexName;
    }
}