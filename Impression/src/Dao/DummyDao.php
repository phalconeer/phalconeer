<?php
namespace Phalconeer\Impression\Dao;

use Phalconeer\Dao;
use Phalconeer\Dto;
use Phalconeer\Impression as This;

class DummyDao implements Dao\DaoReadAndWriteInterface
{
    public function daoNotSet()
    {
        throw new This\Exception\UndefinedDaoException(
            '',
            This\Helper\ExceptionHelper::IMPRESSION__DAO_NOT_SET
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

    public function save(
        Dto\ArrayObjectExporterInterface $data,
        bool $forceInsert = false,
        string $insertMode = Dao\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : ?Dto\ImmutableDto
    {
        $this->daoNotSet();
    }

    public function delete(
        array $whereConditions = []
    ) : bool
    {
        $this->daoNotSet();
    }
}