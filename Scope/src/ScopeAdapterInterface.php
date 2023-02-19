<?php
namespace Phalconeer\Scope;

use Phalconeer\AuthMethod;

interface ScopeAdapterInterface
{
    public function getAllowedScopes(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject;

    public function getDeniedScopes(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject;
}