<?php
namespace Phalconeer\AuthenticateDeviceIdAdmin;

use Phalconeer\Application;
use Phalconeer\Bootstrap;
use Phalconeer\AuthAdmin;
use Phalconeer\AuthenticateDeviceId;
use Phalconeer\AuthenticateDeviceIdAdmin as This;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'authenticateDeviceIdAdmin';
    
    protected static array $requiredModules = [
        Application\Factory::MODULE_NAME,
        AuthAdmin\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME,
        User\Factory::MODULE_NAME,
    ];

    protected function configure() {
        $authDao = new AuthenticateDeviceId\Dao\UserCredentialsDevicesDao(
            $this->di->get(MySqlAdapter\Factory::MODULE_NAME, ['auth', false])
        );
        $authAdmin = $this->di->get(AuthAdmin\Factory::MODULE_NAME);
        $user = $this->di->get(User\Factory::MODULE_NAME);
        $application = $this->di->get(Application\Factory::MODULE_NAME);

        $bo = new This\Bo\AuthenticateDeviceIdAdminBo(
            $authDao,
            $user,
            $application
        );
        
        $authAdmin->addAuthenticationCreator($bo);

        return $bo;
    }
}