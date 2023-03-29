<?php
namespace Phalconeer\AuthenticateCredentials\Bo;

use Phalconeer\Auth;
use Phalconeer\AuthMethod;
use Phalconeer\Dao;
use Phalconeer\AuthenticateCredentials as This;
use Phalcon\Config as PhalconConfig;

class AuthenticateCredentialsBo implements Auth\AuthenticatorInterface
{
    public function __construct(
        protected Dao\DaoReadInterface $authDao,
        protected PhalconConfig\Config $config
    )
    {
    }

    public function authenticate (AuthMethod\Data\AuthenticationRequest $authenticationRequest) : AuthMethod\Data\AuthenticationResponse
    {
        $credentialData = $this->authDao->getRecord([
            'username'          => $authenticationRequest->username(),
        ]);

        if (is_null($credentialData)) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => This\Factory::MODULE_NAME,
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND
            ]);
        }
        $credential = new This\Data\TUserCredential($credentialData);
        if (!password_verify($authenticationRequest->password(), $credential->password())) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => This\Factory::MODULE_NAME,
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND
            ]);
        }

        return AuthMethod\Data\AuthenticationResponse::fromArray([
            'method'            => This\Factory::MODULE_NAME,
            'sessionValid'      => true,
            'userId'            => $credential->userId(),
        ]);
    }

    public function getMethodName() : string
    {
        return This\Factory::MODULE_NAME;
    }
}