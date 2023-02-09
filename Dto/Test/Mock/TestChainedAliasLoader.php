<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestChainedAliasLoader extends This\Mock\TestAliasLoader
{
    use Dto\Traits\AliasLoader,
        Data\Traits\Data\ParseTypes;

    protected static array $_properties = [
        'nestedObject'          => TestChainedAliasLoader::class,
    ];

    protected static array $_loadAliases = [
        'externalIntProperty'        => 'intProperty'
    ];
}