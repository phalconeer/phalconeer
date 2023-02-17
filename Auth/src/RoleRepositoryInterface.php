<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;

interface RoleRepositoryInterface
{
    public function getUserRoles(int $userId = null) : This\Data\RoleCollection
;
}