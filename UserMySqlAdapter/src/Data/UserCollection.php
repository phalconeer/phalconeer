<?php
namespace Phalconeer\UserMySqlAdapter\Data;

use Phalconeer\Dto;

class UserCollection extends Dto\ImmutableCollectionDto
{
    protected $collectionType = User::class;
}