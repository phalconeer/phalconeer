<?php
namespace Phalconeer\User;

use Phalconeer\Bootstrap;
use Phalconeer\Auth;
use Phalconeer\MySqlAdapter;
use Phalconeer\User as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'user';
    
    protected static array $requiredModules = [
        Auth\Factory::MODULE_NAME,
        MySqlAdapter\Factory::MODULE_NAME,
    ];
    
    protected function configure() {
        return new This\Bo\UserBo(
            new This\Dao\DummyDao(),
            This\Data\User::class,
            This\Data\UserCollection::class
        );
    }
}