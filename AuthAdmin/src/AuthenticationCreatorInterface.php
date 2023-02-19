<?php
namespace Phalconeer\AuthAdmin;

use Phalconeer\Auth;

interface AuthenticationCreatorInterface
{
    public function create (Auth\Data\AuthenticationRequest $authenticationRequest) : bool;

    public function getMethodName() : string;
}