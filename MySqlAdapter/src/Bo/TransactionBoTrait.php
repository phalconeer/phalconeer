<?php
namespace Phalconeer\MySqlAdapter\Bo;

use Phalconeer\Dao;
use Phalconeer\MySqlAdapter as This;

trait TransactionBoTrait
{
    protected static $connectionsInTransaction = [];

    public function startTransaction(Dao\DaoReadAndWriteInterface $dao = null) : self
    {
        /**
         * Tramsaction is only set on one DAO, but the transaction will be open for all DAOs
         * connecting to the same database (unless the MySqlAdapter cache was ignored with forceNew)
         */
        if (is_null($dao)) {
            $dao = $this->getDao();
        }

        if (!$dao instanceof This\TransactionDaoInterface) {
            throw new This\Exception\TransactionNotAllowedForDaoException(get_class($dao), This\Helper\ExceptionHelper::MYSQL_ADAPTER__DAO_CAN_NOT_START_TRANSACTION);
        }
        $dao->startTransaction();
        static::$connectionsInTransaction[] = $dao;

        return $this;
    }

    public function rollbackTransaction() : void
    {
        foreach (static::$connectionsInTransaction as $dao) {
            $dao->rollbackTransaction();
        }
        static::$connectionsInTransaction = [];
    }

    public function commitTransaction() : void
    {
        foreach (static::$connectionsInTransaction as $dao) {
            $dao->commitTransaction();
        }
        static::$connectionsInTransaction = [];
    }
}
