<?php

namespace Phalconeer\Auth;

use Phalconeer\Bootstrap;
use Phalconeer\Application;
use Phalconeer\Auth as This;
use Phalconeer\LiveSession;
use Phalconeer\MySqlAdapter;


class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'auth';
    
    protected static array $requiredModules = [
        Application\Factory::MODULE_NAME,
        LiveSession\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME
    ];
    
    protected static array $configFiles = [
        __DIR__ . '/_config/exception_descriptors_config.php'
    ];

    protected function configure() {
        $mysqlAdapter = $this->di->get('mysqlAdapter', ['auth', false]);
        $application = $this->di->get(ApplicationModule::MODULE_NAME);
        return new This\Bo\AuthenticationBo(
            $this->di->get('liveSession'),
            new This\Bo\UserAuthenticationBo(),
            new This\Bo\UserAuthenticationCreatorBo(),
            new This\Bo\RoleRepository(
                $application,
                new This\Dao\ApplicationPrivilegeSchemeRolesDao($mysqlAdapter),
                new This\Dao\UserRolesDao($mysqlAdapter)
            ),
            new This\Bo\PrivilegeRepository(
                $application,
                new This\Dao\RolePrivilegesDao($mysqlAdapter),
                new This\Dao\UserPrivilegesDao($mysqlAdapter)
            ),
            new This\Bo\ScopeRepository(
                $application,
                new This\Dao\PrivilegesDao($mysqlAdapter)
            )
        );
    }
}