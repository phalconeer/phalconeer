<?php
namespace Phalconeer\UserMySqlAdapter\Data;

use Phalconeer\Dto;

class UserCollection extends Dto\ImmutableDtoCollection
{
    protected $collectionType = User::class;
}