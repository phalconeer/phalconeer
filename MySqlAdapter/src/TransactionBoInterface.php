<?php
namespace Phalconeer\MySqlAdapter;

use Phalconeer\Dao;

interface TransactionBoInterface
{
    public function getDao() : Dao\DaoReadInterface;
}