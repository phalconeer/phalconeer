<?php
namespace Phalconeer\Module\UserAdmin\Bo;

use Phalconeer\Helper;
use Phalconeer\Module\Auth;
use Phalconeer\Module\Dao;
use Phalconeer\Module\LiveSession;
use Phalconeer\Module\MySqlAdapter;
use Phalconeer\Module\User;

class UserAdminBo implements MySqlAdapter\TransactionBoInterface, Auth\LoginSuccessfulHandlerInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

    /**
     * @var \Phalconeer\Module\User\Bo\UserFactory
     */
    protected $bo;

    /**
     * @var \Phalconeer\Module\User\Dao\UsersDao
     */
    protected $dao;

    public function __construct(
        User\Bo\UserFactory $userFactory,
        Dao\DaoReadAndWriteInterface $dao
    )
    {
        $this->userFactory = $userFactory;
        $this->dao = $dao;
    }

    public function getDao() : Dao\DaoReadAndWriteInterface
    {
        return $this->dao;
    }

    public function saveUser(User\Dto\User $user, $forceInsert = false) : User\Dto\User
    {
        return $this->dao->save($user, $forceInsert);
    }

    public function create(User\Dto\User $user) : User\Dto\User
    {
        return $this->saveUser($user);
    }

    public function handleLogin() : callable
    {
        return function (LiveSession\Dto\LiveSession $sessionObject) {
            if (is_null($sessionObject->userId())) {
                return;
            }

            $bo = $this->userFactory->getUserById($sessionObject->userId(), false, false);
            $bo->setLastLogin(Helper\DateHelper::getCurrentTimestamp());
            $this->saveUser($bo->getUser());
        };
    }
}