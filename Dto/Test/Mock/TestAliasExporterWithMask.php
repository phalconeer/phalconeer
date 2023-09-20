<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporterWithMask extends This\Mock\Test
{
    use Dto\Trait\AliasExporter;

    protected static array $properties = [
        'nestedObject'          => TestAliasExporterWithMask::class,
    ];

    protected static array $exportAliases = [
        'stringProperty'            => 'externalStringProperty',
        'boolProperty'              => null,
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\AliasExporter::TRAIT_METHOD,
    ];
}