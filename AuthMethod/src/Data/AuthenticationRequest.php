<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;

class AuthenticationRequest extends Data\ImmutableData
{
    use Data\Traits\Data\ParseTypes,
        Data\Traits\Data\AutoGetter;

    protected string $method;

    protected string $password;

    protected string $requestId;

    protected \DateTime $requestTime;

    protected string $sessionId;

    protected int $userId;

    protected string $username;
}