<?php

namespace Phalconeer\MySqlAdapter\Trait;

use Phalconeer\Dao;

trait Transaction
{
    protected bool $isTransactionInProgress = false;

    public function startTransaction() : void
    {
        if ($this->isTransactionInProgress) {
            return;
        }
        $this->isTransactionInProgress = true;
        $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE)->begin();
    }

    public function rollbackTransaction() : void
    {
        $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE)->rollback();
        $this->isTransactionInProgress = false;
    }

    public function commitTransaction() : void
    {
        $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE)->commit();
        $this->isTransactionInProgress = false;
    }

    public function isUnderTransaction() : bool
    {
        return $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE)->isUnderTransaction()
                && $this->isTransactionInProgress;
    }

    public function __destruct()
    {
        if ($this->isUnderTransaction()) {
            $this->rollbackTransaction();
        }
    }
}
