<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasLoader extends This\Mock\Test
{
    use Dto\Traits\AliasLoader,
        Data\Traits\Data\ParseTypes;

    protected static array $_properties = [
        'nestedObject'          => TestAliasLoader::class,
    ];

    protected static array $_loadAliases = [
        'externalStringProperty'        => 'stringProperty'
    ];

    protected static array $_loadTransformers = [
        Dto\Helper\TraitsHelper::LOADER_METHOD_ALIAS,
    ];
}