<?php
namespace Phalconeer\AuthenticateBearer;

use Phalconeer\Auth;
use Phalconeer\Bootstrap;
use Phalconeer\LiveSession;
use Phalconeer\AuthenticateBearer as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authenticateBearer';
    
    protected static array $requiredModules = [
        Auth\Factory::MODULE_NAME,
        LiveSession\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/exception_descriptors_config.php'
    ];

    protected function configure() {
        $this->di->get(Auth\Factory::MODULE_NAME)->addAuthenticator(
            new This\Bo\AuthenticateBearerBo(
                $this->di->get(LiveSession\Factory::MODULE_NAME)
            )
        );
    }
}