<?php
namespace Phalconeer\User\Data;

use Phalconeer\Dto;

class UserCollection extends Dto\ImmutableDtoCollection
{
    protected $collectionType = User::class;
}