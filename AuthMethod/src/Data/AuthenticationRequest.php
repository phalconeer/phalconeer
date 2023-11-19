<?php
namespace Phalconeer\AuthMethod\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class AuthenticationRequest extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected string $method;

    protected string $password;

    protected string $requestId;

    protected \DateTime $requestTime;

    protected string $sessionId;

    protected int $userId;

    protected string $username;
}