<?php
namespace Phalconeer\UserMySqlAdapter\Dao;

use Phalconeer\MySqlAdapter;

class UsersDao extends MySqlAdapter\Dao\SqlDaoBase implements MySqlAdapter\TransactionDaoInterface
{
    use MySqlAdapter\Dao\TransactionDaoTrait;

    protected string $tableName = 'users';
}