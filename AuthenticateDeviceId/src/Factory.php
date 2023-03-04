<?php
namespace Phalconeer\AuthenticateDeviceId;

use Phalconeer\Application;
use Phalconeer\Bootstrap;
use Phalconeer\Auth;
use Phalconeer\AuthAdmin;
use Phalconeer\AuthenticateDeviceId as This;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authenticateDeviceId';
    
    protected static array $requiredModules = [
        Application\Factory::MODULE_NAME,
        Auth\Factory::MODULE_NAME,
        AuthAdmin\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME,
        User\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/exception_descriptors_config.php'
    ];

    protected function configure() {
        $authDao = new This\Dao\UserCredentialsDevicesDao($this->di->get(MySqlAdapter\Factory::MODULE_NAME, ['auth']));
        $auth = $this->di->get(Auth\Factory::MODULE_NAME);
        $authAdmin = $this->di->get(AuthAdmin\Factory::MODULE_NAME);

        $bo = new This\Bo\AuthenticateDeviceIdBo(
            $authDao,
            $this->di->get(User\Factory::MODULE_NAME),
            $this->di->get(Application\Factory::MODULE_NAME)
        );
        $auth->addAuthenticator($bo);
        
        $authAdmin->addAuthenticationCreator(new This\Bo\AuthenticateDeviceIdBo(
            new This\Dao\UserCredentialsDevicesDao($this->di->get(MySqlAdapter\Factory::MODULE_NAME, ['auth', false])),
            $this->di->get(User\Factory::MODULE_NAME),
            $this->di->get(Application\Factory::MODULE_NAME)
        ));

        return $bo;
    }
}