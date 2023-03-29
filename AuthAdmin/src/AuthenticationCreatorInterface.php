<?php
namespace Phalconeer\AuthAdmin;

use Phalconeer\AuthMethod;
use Phalconeer\Dto;

interface AuthenticationCreatorInterface
{
    public function create(AuthMethod\Data\AuthenticationRequest $authenticationRequest) : Dto\ImmutableDto;

    public function getMethodName() : string;
}