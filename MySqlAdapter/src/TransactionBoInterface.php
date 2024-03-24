<?php
namespace Phalconeer\MySqlAdapter;

use Phalconeer\Dao;

interface TransactionBoInterface
{
    public function getDao() : Dao\DaoReadInterface;

    public function startTransaction(Dao\DaoReadAndWriteInterface $dao = null) : self;

    public function rollbackTransaction() : void;

    public function commitTransaction() : void;
}