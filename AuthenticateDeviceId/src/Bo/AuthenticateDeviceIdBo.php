<?php
namespace Phalconeer\AuthenticateDeviceId\Bo;

use Phalconeer\Auth;
use Phalconeer\AuthAdmin;
use Phalconeer\AuthMethod;
use Phalconeer\Dao;
use Phalconeer\Application;
use Phalconeer\AuthenticateDeviceId as This;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class AuthenticateDeviceIdBo
    implements Auth\AuthenticatorInterface,
        AuthAdmin\AuthenticationCreatorInterface,
        MySqlAdapter\TransactionBoInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

    const METHOD_NAME = 'authenticateDeviceId';

    public function __construct(
        protected Dao\DaoReadAndWriteInterface $authDao,
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
            return new AuthMethod\Data\AuthenticationResponse(new \ArrayObject([
                'method'            => self::METHOD_NAME,
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__CREDENTIALS_NOT_FOUND
            ]));
        }

        $credential = new This\Data\UserCredentialDevice($credentialData);

        $userData = $this->userBo->getUser([
            'id'            => $credential->userId(),
            'applicationId' => $this->application->getId()
        ]);
        if (is_null($userData)) {
            return new AuthMethod\Data\AuthenticationResponse(new \ArrayObject([
                'method'            => self::METHOD_NAME,
                'error'             => This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__APPLICATION_NOT_LINKED
            ]));
        }

        return new AuthMethod\Data\AuthenticationResponse(new \ArrayObject([
            'userId'            => $credential->userId(),
            'sessionValid'      => $credential->isValid(),
            'method'            => self::METHOD_NAME
        ]));
    }

    public function create(AuthMethod\Data\AuthenticationRequest $authenticationRequest) : bool
    {
        $existingDeviceId = $this->authDao->getRecord([
            'deviceId'      => $authenticationRequest->username(),
        ]);
        $existingUserId = $this->authDao->getRecord([
            'userId'        => $authenticationRequest->userId(),
        ]);

        if (!is_null($existingDeviceId)) {
            throw new This\Exception\DeviceIdExist($authenticationRequest->username(), This\Helper\ExceptionHelper::CREATE_MEMBER_TOKEN__DEVICE_ID_EXISTS);
        }

        if (!is_null($existingUserId)) {
            $credential = new This\Data\UserCredentialDevice(null, $existingUserId);
        } else {
            $credential = new This\Data\UserCredentialDevice(new \ArrayObject([
                'userId'            => $authenticationRequest->userId()
            ]));
        }

        $credential = $credential
                        ->changeDeviceId(
                            $authenticationRequest->username()
                        );

        return !is_null($this->authDao->save($credential));
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
        return static::METHOD_NAME;
    }
}