<?php
namespace Phalconeer\Auth;

interface ScopeRepositoryInterface
{
    public function getScopeNames(array $privileges, string $privilegeScheme = null) : \ArrayObject;
}