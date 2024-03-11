<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporter extends This\Mock\Test implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\AliasExporter;

    protected static array $properties = [
        'nestedObject'              => TestAliasExporter::class,
    ];

    protected static array $exportAliases = [
        'stringProperty'            => 'externalStringProperty'
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\AliasExporter::class,
    ];
}