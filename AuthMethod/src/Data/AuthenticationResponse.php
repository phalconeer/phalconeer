<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;

class AuthenticationResponse extends Data\ImmutableData
{
    use Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter;

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