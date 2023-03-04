<?php
namespace Phalconeer\UserMySqlAdapter\Data;

use Phalconeer\MySqlAdapter;
use Phalconeer\User as UserModule;

class User extends UserModule\Data\User
{
    use MySqlAdapter\Trait\MySqlBooleanExporter,
        MySqlAdapter\Trait\MySqlBooleanLoader,
        MySqlAdapter\Trait\MySqlDateExporter,
        MySqlAdapter\Trait\MySqlDateLoader;

    protected static array $_exportTransformers = [
        MySqlAdapter\Transformer\MySqlBooleanExporter::TRAIT_METHOD,
        MySqlAdapter\Transformer\MySqlDateExporter::TRAIT_METHOD,
    ];

    protected static array $_loadTransformers = [
        MySqlAdapter\Transformer\MySqlBooleanLoader::TRAIT_METHOD,
        MySqlAdapter\Transformer\MySqlDateLoader::TRAIT_METHOD,
    ];
}