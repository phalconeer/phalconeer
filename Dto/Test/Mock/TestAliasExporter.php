<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporter extends This\Mock\Test
{
    use Dto\Trait\AliasExporter;

    protected static array $_properties = [
        'nestedObject'          => TestAliasExporter::class,
    ];

    protected static array $_exportAliases = [
        'stringProperty'            => 'externalStringProperty'
    ];

    protected static array $_exportTransformers = [
        Dto\Transformer\AliasExporter::TRAIT_METHOD,
    ];
}