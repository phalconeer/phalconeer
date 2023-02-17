<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Dao;
use Phalconeer\Application;
use Phalconeer\Auth as This;

class RoleRepository implements This\RoleRepositoryInterface
{
    public function __construct(
        protected Application\ApplicationInterface $application,
        protected Dao\DaoReadInterface $applicationRolesDao,
        protected Dao\DaoReadInterface $userRolesDao
    )
    {
        $this->application = $application;
        $this->applicationRolesDao = $applicationRolesDao;
        $this->userRolesDao = $userRolesDao;
    }

    public function getApplicationRoles(int $userId = null) : This\Data\RoleCollection
    {
        return new This\Data\RoleCollection(
            null,
            $this->applicationRolesDao->getRecords([
                'privilegeScheme'       => $this->application->getPrivilegeScheme(),
                'type'                  => (is_null($userId))
                    ? This\Helper\AuthHelper::PRIVILEGE_SCHEME_ROLE_GUEST
                    : This\Helper\AuthHelper::PRIVILEGE_SCHEME_ROLE_USER
            ]));
    }

    public function getUserRoles(int $userId = null) : This\Data\RoleCollection
    {
        if (is_null($userId)) {
            return new This\Data\RoleCollection();
        }
        return new This\Data\RoleCollection(null, $this->userRolesDao->getRecords([
            'userId'        => $userId
        ]));
    }

    public function assignUserRole($userId, $roleId) : bool
    {
        $userRoleDto = new This\Data\TUserRole(new \ArrayObject([
            'userId'            => $userId,
            'roleId'            => $roleId,
        ]));

        return !is_null($this->userRolesDao->save($userRoleDto));
    }
}