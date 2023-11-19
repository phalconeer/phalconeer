<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class AuthenticationResponse extends Data\ImmutableData
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected \ArrayObject $deniedPermissions;

    protected string $error;

    protected string $method;

    protected \ArrayObject $roles;

    protected \ArrayObject $scopes;

    protected string $sessionId;

    protected bool $sessionValid;

    protected int $userId;

    public function setScopes(?\ArrayObject $scopes) : self
    {
        return $this->setValueByKey('scopes', $scopes);
    }

    public function setDeniedPermissions(?\ArrayObject $deniedPermissions) : self
    {
        return $this->setValueByKey('deniedPermissions', $deniedPermissions);
    }
}