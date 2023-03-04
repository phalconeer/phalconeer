<?php
namespace Phalconeer\User\Data;

use Phalconeer\Dto;

class UserCollection extends Dto\ImmutableCollectionDto
{
    protected $collectionType = User::class;
}