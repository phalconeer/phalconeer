<?php
namespace Phalconeer\UserLoggedIn;

use Phalconeer\User;

interface UserLoggedInInterface
{
    public function getLoggedIn() : ?User\UserInterface;
}