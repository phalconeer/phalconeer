<?php
namespace Phalconeer\UserLoggedIn;

use Phalconeer\Bootstrap;
use Phalconeer\Auth;
use Phalconeer\User;
use Phalconeer\UserLoggedIn as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'userLoggedIn';
    
    protected static array $requiredModules = [
        User\Factory::MODULE_NAME,
        Auth\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        $auth = $this->di->get(Auth\Factory::MODULE_NAME);
        $user = $this->di->get(User\Factory::MODULE_NAME);

        return function(User\Bo\UserBo $userBo = null) use ($auth, $user) {
            if (is_null($userBo)) {
                $userBo = $user;
            }
            $bo = new This\Bo\UserLoggedInBo($userBo);

            $auth->addLoginHandler($bo->handleLogin());
            $auth->addLogoutHandler($bo->handleLogout());

            return $bo;
        };
    }
}