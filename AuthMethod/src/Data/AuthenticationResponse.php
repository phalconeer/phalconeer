<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;

class AuthenticationResponse extends Data\ImmutableData
{
    use Data\Traits\Data\ParseTypes,
        Data\Traits\Data\AutoGetter;

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
        return $this->setKeyValue('scopes', $scopes);
    }

    public function setDeniedPermissions(?\ArrayObject $deniedPermissions) : self
    {
        return $this->setKeyValue('deniedPermissions', $deniedPermissions);
    }
}