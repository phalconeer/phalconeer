<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;

interface AuthenticatorInterface
{
    public function authenticate (This\Data\AuthenticationRequest $authenticationRequest) : This\Data\AuthenticationResponse;

    public function getMethodName() : string;
}