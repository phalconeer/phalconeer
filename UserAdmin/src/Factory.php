<?php

namespace Phalconeer\UserAdmin;

use Phalconeer\Bootstrap;
use Phalconeer\Auth;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;
use Phalconeer\UserAdmin as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'userAdmin';
    
    protected static array $requiredModules = [
        Auth\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME,
        User\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        $mysqlAdapter = $this->di->get(MySqlAdapter\Factory::MODULE_NAME, ['user', false]);
        $userAdmin = new This\Bo\UserAdminBo(
            $this->di->get(User\Factory::MODULE_NAME),
            new This\Dao\DummyDao($mysqlAdapter),
        );

        $this->di->get(Auth\Factory::MODULE_NAME)->addLoginHandler($userAdmin->handleLogin());

        return $userAdmin;
    }
}