<?php
namespace Phalconeer\Auth;

use Phalconeer\Auth as This;

interface AuthenticationCreatorInterface
{
    public function create (This\Data\AuthenticationRequest $authenticationRequest) : bool;

    public function getMethodName() : string;
}