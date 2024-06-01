<?php
namespace Phalconeer\RateLimiter\Dao;

use Phalconeer\Dao;

class AllowAllDao implements Dao\DaoReadInterface
{
    public function getRecord(
        array $whereConditions = []
    ) : ?\ArrayObject
    {
        return new \ArrayObject();
    }

    public function getRecords(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?\ArrayObject
    {
        return new \ArrayObject();
    }

    public function getCount(array $whereConditions = []) : int
    {
        return 0;
    }
}