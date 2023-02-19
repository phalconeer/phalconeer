<?php

namespace Phalconeer\Auth;

use Phalconeer\Bootstrap;
use Phalconeer\Auth as This;
use Phalconeer\LiveSession;
use Phalconeer\Scope;


class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'auth';
    
    protected static array $requiredModules = [
        LiveSession\Factory::MODULE_NAME,
        Scope\Factory::MODULE_NAME
    ];
    
    protected static array $configFiles = [
        __DIR__ . '/_config/exception_descriptors_config.php'
    ];

    protected function configure() {
        return new This\Bo\AuthenticationBo(
            $this->di->get(LiveSession\Factory::MODULE_NAME),
            $this->di->get(Scope\Factory::MODULE_NAME),
        );
    }
}