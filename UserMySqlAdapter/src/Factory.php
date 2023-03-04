<?php
namespace Phalconeer\UserMySqlAdapter;

use Phalconeer\Bootstrap;
use Phalconeer\Auth;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;
use Phalconeer\UserMySqlAdapter as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'userMySqlAdapter';
    
    protected static array $requiredModules = [
        Auth\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME,
        User\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        return new User\Bo\UserBo(
            new This\Dao\UsersDao(),
            This\Data\User::class,
            This\Data\UserCollection::class
        );
    }
}