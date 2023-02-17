<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;

interface PrivilegeRepositoryInterface
{
     public function getPrivilegesByRoles(This\Data\RoleCollection $roles) : This\Data\PrivilegeCollection;

     public function getPrivilegesByUser(int $userId = null) : ?This\Data\PrivilegeCollection;
}