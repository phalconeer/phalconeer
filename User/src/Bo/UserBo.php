<?php
namespace Phalconeer\User\Bo;

use Phalconeer\Dao;
use Phalconeer\MySqlAdapter;
use Phalconeer\User as This;

class UserBo implements MySqlAdapter\TransactionBoInterface
{
    use MySqlAdapter\Bo\TransactionBoTrait;

    public function __construct(
        protected Dao\DaoReadInterface $dao,
        protected ?string $userClass = This\Dto\User::class,
        protected ?string $collectionClass = This\Dto\UserCollection::class
    )
    {
    }

    public function getDao() : Dao\DaoReadInterface
    {
        return $this->dao;
    }

    public function getUser(array $whereConditions) : ?This\UserInterface
    {
        $result = $this->dao->getRecord($whereConditions);

        if (is_null($result)) {
            throw new This\Exception\UserNotFoundException(
                get_class($this->dao) . ': ' .json_encode($whereConditions),
                This\Helper\ExceptionHelper::USER__USER_NOT_FOUND
            );
        }

        return new $this->userClass($result);
    }

    public function getUsers(
        array $whereConditions = [],
        $limit = 10,
        $offset = 0,
        $orderString = ''
    ) : ?This\Data\UserCollection
    {
        $userData = $this->dao->getRecords(
            $whereConditions,
            $limit,
            $offset,
            $orderString
        );

        return (is_null($userData))
            ? null
            : new $this->collectionClass(null, $userData);
    }
}