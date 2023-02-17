<?php
namespace Phalconeer\MySqlAdapter;

use Phalconeer\Dao\DaoReadInterface;

interface TransactionBoInterface
{
    public function getDao() : DaoReadInterface;
}