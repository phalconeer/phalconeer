<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;

interface ScopeAdapterInterface
{
    public function getScopeNames(This\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject;
}