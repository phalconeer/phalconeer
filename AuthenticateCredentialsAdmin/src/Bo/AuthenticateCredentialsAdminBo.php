<?php
namespace Phalconeer\AuthenticateCredentialsAdmin\Bo;

use Phalconeer\AuthAdmin;
use Phalconeer\AuthMethod;
use Phalconeer\Dao;
use Phalconeer\Dto;
use Phalconeer\AuthenticateCredentials;
use Phalconeer\AuthenticateCredentialsAdmin as This;
use Phalcon\Config as PhalconConfig;

class AuthenticateCredentialsBo implements AuthAdmin\AuthenticationCreatorInterface
{
    public function __construct(
        protected Dao\DaoReadAndWriteInterface $authDao,
        protected PhalconConfig\Config $config
    )
    {
    }

    public function encodePassword(string $password) : string
    {
        return password_hash(
            $password,
            $this->config->get('algorhytm', PASSWORD_BCRYPT),
            [
                'cost' => $this->config->get('cost', 14)
            ]
        );
    }

    public function create(AuthMethod\Data\AuthenticationRequest $authenticationRequest) : Dto\ImmutableDto
    {
        $isUsernameUnique = $this->authDao->getCount([
            'username'      => $authenticationRequest->username(),
        ]);
        if ($isUsernameUnique > 0) {
            throw new This\Exception\LoginCredentialExist(
                $authenticationRequest->username(),
                This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIAL_NOT_UNIQUE
            );
        }

        $isUserIdUnique = $this->authDao->getCount([
            'userId'        => $authenticationRequest->userId(),
        ]);

        if ($isUsernameUnique > 0
            || $isUserIdUnique > 0) {
            throw new This\Exception\LoginCredentialExist(
                $authenticationRequest->userId(),
                This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIAL_EXISTS
            );
        }

        $toSave = AuthenticateCredentials\Data\TUserCredential::fromArray([
            'password'          => $this->encodePassword($authenticationRequest->password()),
            'userId'            => $authenticationRequest->userId(),
            'username'          => $authenticationRequest->username(),
        ]);

        return $this->authDao->save($toSave);
    }

    public function getMethodName() : string
    {
        return This\Factory::MODULE_NAME;
    }
}