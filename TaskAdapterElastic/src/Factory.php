<?php
namespace Phalconeer\TaskAdapterElastic;

use Phalconeer\Bootstrap;
use Phalconeer\ElasticAdapter;
use Phalconeer\Task;
use Phalconeer\TaskAdapterElastic as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'taskAdapterElastic';

    protected static array $requiredModules = [
        ElasticAdapter\Factory::MODULE_NAME,
        Task\Factory::MODULE_NAME,
    ];

    protected function configure()
    {
        $adapter = $this->di->get(ElasticAdapter\Factory::MODULE_NAME);
        return function (string $indexName = null) use ($adapter) : Task\TaskDaoInterface
        {
            $dao = new This\Dao\TasksDao($adapter);
            if (!is_null($indexName)) {
                $dao->indexName = $indexName;
            }

            return $dao;
        };
    }
}