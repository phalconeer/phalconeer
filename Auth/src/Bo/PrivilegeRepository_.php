<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Dao;
use Phalconeer\Application;
use Phalconeer\Auth as This;

class PrivilegeRepository implements This\PrivilegeRepositoryInterface
{
    public function __construct(
        protected Application\ApplicationInterface $application,
        protected This\RolePrivilegesDaoInterface $rolePrivilegesDao,
        protected Dao\DaoReadInterface $userPrivilegesDao
    )
    {
        $this->application = $application;
        $this->rolePrivilegesDao = $rolePrivilegesDao;
        $this->userPrivilegesDao = $userPrivilegesDao;
    }

    public function getPrivilegesByRoles(This\Data\RoleCollection $roles) : This\Data\PrivilegeCollection
    {
        $iterator = $roles->getIterator();
        $privileges = new This\Data\PrivilegeCollection();
        while ($iterator->valid()) {
            $privileges = $privileges->merge(new This\Data\PrivilegeCollection(
                null,
                $this->rolePrivilegesDao->getCascadingRoles($iterator->current()->roleId())
            ));
            $iterator->next();
        }
        return $privileges;
    }

    public function getPrivilegesByUser(int $userId = null) : ?This\Data\PrivilegeCollection
    {
        if (is_null($userId)) {
            return null;
        }
        $data = $this->userPrivilegesDao->getRecords([
            'userId'        => $userId
        ]);
        return (is_null($data))
            ? null
            : new This\Data\PrivilegeCollection(null, $data);
    }
}