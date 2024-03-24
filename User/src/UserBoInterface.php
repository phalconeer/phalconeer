<?php
namespace Phalconeer\User;

use Phalconeer\User as This;

interface UserBoInterface
{
    public function getUser(array $whereConditions) : ?This\UserInterface;

    public function getUsers(
        array $whereConditions = [],
        $limit = 10,
        $offset = 0,
        $orderString = ''
    ) : ?This\Data\UserCollection;

    public function setCollectionClass(string $collectionClass) : self;

    public function setUserClass(string $userClass) : self;
}