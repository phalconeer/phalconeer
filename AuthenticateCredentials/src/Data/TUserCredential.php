<?php
namespace Phalconeer\AuthenticateCredentials\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class TUserCredential extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected ?int $userId;

    protected string $password;

    protected ?string $username;

    public function setUsername(string $loginId) : self
    {
        return $this->setValueByKey('loginId', $loginId);
    }

    public function setPassword(string $password) : self
    {
        return $this->setValueByKey('password', $password);
    }
}