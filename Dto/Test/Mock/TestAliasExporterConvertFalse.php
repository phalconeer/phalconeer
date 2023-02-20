<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporterConvertFalse extends This\Mock\Test
{
    use Dto\Trait\AliasExporter;

    protected static bool $_convertChildren = false;

    protected static array $_properties = [
        'nestedObject'          => TestAliasExporterConvertFalse::class,
    ];

    protected static array $_exportAliases = [
        'stringProperty'            => 'externalStringProperty'
    ];

    protected static array $_exportTransformers = [
        Dto\Transformer\AliasExporter::TRAIT_METHOD,
    ];
}