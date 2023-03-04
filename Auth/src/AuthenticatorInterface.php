<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;
use Phalconeer\AuthMethod;

interface AuthenticatorInterface
{
    public function authenticate (AuthMethod\Data\AuthenticationRequest $authenticationRequest) : AuthMethod\Data\AuthenticationResponse;

    public function getMethodName() : string;
}