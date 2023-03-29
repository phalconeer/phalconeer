<?php
namespace Phalconeer\AuthenticateCredentials;

use Phalconeer\Auth;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Dao;
use Phalconeer\AuthenticateCredentials as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authenticateCredentials';
    
    protected static array $requiredModules = [
        Auth\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/authenticate_credentials_config.php',
    ];

    protected function getDao() : Dao\DaoReadInterface
    {
        return new This\Dao\DummyDao();
    }

    protected function configure() {
        $auth = $this->di->get(Auth\Factory::MODULE_NAME);
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(self::MODULE_NAME);
        
        $bo = new This\Bo\AuthenticateCredentialsBo(
            $this->getDao(),
            $config
        );
        $auth->addAuthenticator($bo);

        return $bo;
    }
}