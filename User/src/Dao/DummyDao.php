<?php
namespace Phalconeer\User\Dao;

use Phalconeer\Dao;
use Phalconeer\User as This;

class DummyDao implements Dao\DaoReadInterface
{
    protected function daoNotSet()
    {
        throw new This\Exception\DaoNotSetException(
            '',
            This\Helper\ExceptionHelper::USER__DAO_NOT_SET
        );
    }

    public function getRecord(
        array $whereConditions = []
    ) : ?\ArrayObject
    {
        $this->daoNotSet();
    }

    public function getRecords(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?\ArrayObject
    {
        $this->daoNotSet();
    }

    public function getCount(array $whereConditions = []) : int
    {
        $this->daoNotSet();
    }
}