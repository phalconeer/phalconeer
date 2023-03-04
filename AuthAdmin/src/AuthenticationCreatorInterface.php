<?php
namespace Phalconeer\AuthAdmin;

use Phalconeer\AuthMethod;

interface AuthenticationCreatorInterface
{
    public function create (AuthMethod\Data\AuthenticationRequest $authenticationRequest) : bool;

    public function getMethodName() : string;
}