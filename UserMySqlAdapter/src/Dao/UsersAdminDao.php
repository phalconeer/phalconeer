<?php
namespace Phalconeer\UserMySqlAdapter\Dao;

use Phalconeer\UserMySqlAdapter as This;
use Phalconeer\MySqlAdapter;

class UsersAdminDao extends This\Dao\UsersDao implements MySqlAdapter\TransactionDaoInterface
{
    use MySqlAdapter\Trait\Transaction;
}