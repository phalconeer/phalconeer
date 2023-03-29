<?php
namespace Phalconeer\AuthenticateDeviceIdAdmin\Bo;

use Phalconeer\AuthAdmin;
use Phalconeer\AuthMethod;
use Phalconeer\Dao;
use Phalconeer\Dto;
use Phalconeer\Application;
use Phalconeer\AuthenticateDeviceId;
use Phalconeer\AuthenticateDeviceIdAdmin as This;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class AuthenticateDeviceIdAdminBo
    implements AuthAdmin\AuthenticationCreatorInterface,
        MySqlAdapter\TransactionBoInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

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

    public function create(AuthMethod\Data\AuthenticationRequest $authenticationRequest) : Dto\ImmutableDto
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
            $credential = new AuthenticateDeviceId\Data\UserCredentialDevice(new \ArrayObject([
                'userId'            => $existingUserId
            ]));
        } else {
            $credential = new AuthenticateDeviceId\Data\UserCredentialDevice(new \ArrayObject([
                'userId'            => $authenticationRequest->userId()
            ]));
        }

        $credential = $credential
                        ->changeDeviceId(
                            $authenticationRequest->username()
                        );

        return $this->authDao->save($credential);
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