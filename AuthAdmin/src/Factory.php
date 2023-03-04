<?php
namespace Phalconeer\AuthAdmin;

use Phalconeer\Auth;
use Phalconeer\AuthAdmin as This;
use Phalconeer\Bootstrap;
use Phalconeer\LiveSession;
use Phalconeer\Scope;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authAdmin';

    protected static array $requiredModules = [
        LiveSession\Factory::MODULE_NAME,
        Scope\Factory::MODULE_NAME
    ];

    protected function configure()
    {
        $liveSession = $this->di->get(LiveSession\Factory::MODULE_NAME);
        $scope = $this->di->get(Scope\Factory::MODULE_NAME);

        return new This\Bo\AuthenticationAdminBo(
            $liveSession,
            $scope
        );
    }
}
