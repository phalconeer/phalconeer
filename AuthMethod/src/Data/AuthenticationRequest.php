<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class AuthenticationRequest extends Dto\ImmutableDto
{
    use Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter,
        Dto\Trait\ArrayLoader;

    protected string $method;

    protected string $password;

    protected string $requestId;

    protected \DateTime $requestTime;

    protected string $sessionId;

    protected int $userId;

    protected string $username;
}