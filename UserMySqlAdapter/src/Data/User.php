<?php
namespace Phalconeer\UserMySqlAdapter\Data;

use Phalconeer\MySqlAdapter;
use Phalconeer\User as UserModule;

class User extends UserModule\Data\User
{
    protected static array $exportTransformers = [
        MySqlAdapter\Transformer\MySqlBooleanExporter::class,
        MySqlAdapter\Transformer\MySqlDateExporter::class,
    ];

    protected static array $loadTransformers = [
        MySqlAdapter\Transformer\MySqlBooleanLoader::class,
        MySqlAdapter\Transformer\MySqlDateLoader::class,
    ];
}