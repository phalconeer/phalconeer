<?php
namespace Phalconeer\UserMySqlAdapter\Dao;

use Phalconeer\MySqlAdapter;

class UsersDao extends MySqlAdapter\Dao\SqlDaoBase
{
    protected string $tableName = 'users';
}