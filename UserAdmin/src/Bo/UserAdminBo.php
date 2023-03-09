<?php
namespace Phalconeer\UserAdmin\Bo;

use Phalconeer\Auth;
use Phalconeer\Dao;
use Phalconeer\LiveSession;
use Phalconeer\MySqlAdapter;
use Phalconeer\User;

class UserAdminBo implements MySqlAdapter\TransactionBoInterface, Auth\LoginSuccessfulHandlerInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

    public function __construct(
        protected User\Bo\UserBo $userBo,
        protected Dao\DaoReadAndWriteInterface $dao
    )
    {
    }

    public function getDao() : Dao\DaoReadAndWriteInterface
    {
        return $this->dao;
    }

    public function saveUser(User\Data\User $user, $forceInsert = false) : User\Data\User
    {
        return $this->dao->save($user, $forceInsert);
    }

    public function create(User\Data\User $user) : User\Data\User
    {
        return $this->saveUser($user);
    }

    public function handleLogin() : callable
    {
        return function (LiveSession\Data\LiveSession $sessionObject) {
            if (is_null($sessionObject->userId())) {
                return;
            }

            $user = $this->userBo->getUser([
                'id'        => $sessionObject->userId()
            ]);
            $user = $user->setLastLogin(new \DateTime());
            $this->saveUser($user);
        };
    }
}