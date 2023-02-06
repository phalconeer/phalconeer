<?php
namespace Phalconeer\Dao;

interface DaoReadInterface
{
    public function getRecord(
        array $whereConditions = []
    ) : ?\ArrayObject;

    public function getRecords(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?\ArrayObject;

    public function getCount(array $whereConditions = []) : int;
}
