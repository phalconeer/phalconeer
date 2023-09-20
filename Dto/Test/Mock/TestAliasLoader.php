<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasLoader extends This\Mock\Test
{
    use Dto\Trait\AliasLoader,
        Data\Trait\Data\ParseTypes;

    protected static array $properties = [
        'nestedObject'          => TestAliasLoader::class,
    ];

    protected static array $loadAliases = [
        'externalStringProperty'        => 'stringProperty'
    ];

    protected static array $loadTransformers = [
        Dto\Transformer\AliasLoader::TRAIT_METHOD,
    ];
}