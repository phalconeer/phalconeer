<?php
namespace Phalconeer\MySqlAdapter;

use Phalconeer\Dao\DaoReadAndWriteInterface;

interface TransactionDaoInterface extends DaoReadAndWriteInterface
{
    /**
     * @return void
     */
    public function startTransaction() : void;
    /**
     * @return void
     */
    public function rollbackTransaction() : void;

    /**
     * @return void
     */
    public function commitTransaction() : void;

    /**
     *
     * @return bool
     */
    public function isUnderTransaction() : bool;
}
