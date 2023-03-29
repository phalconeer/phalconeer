<?php
namespace Phalconeer\AuthenticateCredentialsAdmin;

use Phalconeer\AuthAdmin;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\AuthenticateCredentials;
use Phalconeer\AuthenticateCredentialsAdmin as This;

/**
 * Initializes the dispatcher.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authenticateCredentialsAdmin';
    
    protected static array $requiredModules = [
        AuthAdmin\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/login_credentials_config.php',
    ];

    protected function configure() {
        $authAdmin = $this->di->get(AuthAdmin\Factory::MODULE_NAME);
        $authDao = new AuthenticateCredentials\Dao\DummyDao();
        $bo = new This\Bo\AuthenticateCredentialsBo(
            $authDao,
            $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME)
        );
        $authAdmin->addAuthenticationCreator($bo);

        return $bo;
    }
}