<?php
namespace Phalconeer\AuthenticateDeviceId\Dao;

use Phalconeer\MySqlAdapter;

class UserCredentialsDevicesDao extends MySqlAdapter\Dao\SqlDaoBase implements MySqlAdapter\TransactionDaoInterface
{
    use MySqlAdapter\Dao\TransactionDaoTrait;
}