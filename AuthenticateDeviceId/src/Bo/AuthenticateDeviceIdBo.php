<?php
namespace Phalconeer\AuthenticateDeviceId\Bo;

use Phalconeer\Auth;
use Phalconeer\AuthMethod;
use Phalconeer\Dao;
use Phalconeer\Application;
use Phalconeer\AuthenticateDeviceId as This;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class AuthenticateDeviceIdBo
    implements Auth\AuthenticatorInterface,
        MySqlAdapter\TransactionBoInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

    public function __construct(
        protected Dao\DaoReadInterface $authDao,
        protected User\Bo\UserBo $userBo,
        protected Application\ApplicationInterface $application
    )
    {
    }

    public function getDao(): Dao\DaoReadInterface
    {
        return $this->authDao;
    }

    public function authenticate (AuthMethod\Data\AuthenticationRequest $authenticationRequest) : AuthMethod\Data\AuthenticationResponse
    {
        $credentialData = $this->authDao->getRecord([
            'deviceId'       => $authenticationRequest->username(),
        ]);
        if (is_null($credentialData)) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => $this->getMethodName(),
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND
            ]);
        }

        $credential = new This\Data\UserCredentialDevice($credentialData);

        $userData = $this->userBo->getUser([
            'id'            => $credential->userId(),
            'applicationId' => $this->application->getId()
        ]);
        if (is_null($userData)) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => $this->getMethodName(),
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__APPLICATION_NOT_LINKED
            ]);
        }

        return AuthMethod\Data\AuthenticationResponse::fromArray([
            'userId'            => $credential->userId(),
            'sessionValid'      => $credential->isValid(),
            'method'            => $this->getMethodName()
        ]);
    }

    public function assertDeviceIdAlreadyTaken($deviceId) : bool
    {
        $existingDeviceId = $this->authDao->getRecord([
            'deviceId'      => $deviceId,
        ]);

        return (!is_null($existingDeviceId));
    }

    public function getMethodName() : string
    {
        return This\Factory::MODULE_NAME;
    }
}