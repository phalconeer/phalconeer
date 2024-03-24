<?php
namespace Phalconeer\UserLoggedIn\Bo;

use Phalconeer\Auth;
use Phalconeer\LiveSession;
use Phalconeer\User;
use Phalconeer\UserLoggedIn as This;

class UserLoggedInBo implements Auth\LoginSuccessfulHandlerInterface, This\UserLoggedInInterface
{
    protected ?User\Data\User $loggedIn = null;

    public function __construct(
        protected $userBoClass = User\Bo\UserBo::class,
    )
    {
    }

    public function handleLogin() : callable
    {
        return function (LiveSession\Data\LiveSession $sessionObject) {
            if (is_null($sessionObject->userId())) {
                return;
            }

            $this->loggedIn = $this->userBoClass->getUser([
                'id'        => $sessionObject->userId()
            ]);
        };
    }

    public function handleLogout() : callable
    {
        return function (string $sessionId) {
            $this->loggedIn = null;
        };
    }

    public function getLoggedIn() : ?User\UserInterface
    {
        return $this->loggedIn;
    }
}