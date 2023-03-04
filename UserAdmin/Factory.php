<?php

namespace Phalconeer\Module\UserAdmin;

use Phalconeer\BootstrapModule;
use Phalconeer\Module\Auth;
use Phalconeer\Module\MySqlAdapter;
use Phalconeer\Module\User;
use Phalconeer\Module\UserAdmin as This;

/**
 * Initializes the dispatcher.
 */
class Factory extends BootstrapModule
{
    const MODULE_NAME = 'userAdmin';
    
    /**
     *
     * @var array       List of bootstrap modules required to initializes this module. 
     */
    protected static $requiredModules = [
        Auth\AuthModule::MODULE_NAME,
        MySqlAdapter\MySqlAdapterModule::MODULE_NAME,
        User\Factory::MODULE_NAME,
    ];
    
    /**
     * Configures the Bootstrap module
     * @return \Phalcon\Mvc\Dispatcher
     */
    protected function configure() {
        $mysqlAdapter = $this->di->get(MySqlAdapter\MySqlAdapterModule::MODULE_NAME, ['user', false]);
        $userAdmin = new This\Bo\UserAdminBo(
            $this->di->get(User\Factory::MODULE_NAME),
            new User\Dao\UsersDao($mysqlAdapter),
        );

        $this->di->get(Auth\AuthModule::MODULE_NAME)->addLoginHandler($userAdmin->handleLogin());

        return $userAdmin;
    }
}