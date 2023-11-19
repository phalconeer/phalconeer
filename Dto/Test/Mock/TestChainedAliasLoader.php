<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestChainedAliasLoader extends This\Mock\TestAliasLoader
{
    use Dto\Trait\AliasLoader,
        Data\Trait\ParseTypes;

    protected static array $properties = [
        'nestedObject'          => TestChainedAliasLoader::class,
    ];

    protected static array $loadAliases = [
        'externalIntProperty'        => 'intProperty'
    ];
}