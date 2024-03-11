<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporterConvertFalse extends This\Mock\Test implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\AliasExporter;

    protected static bool $convertChildren = false;

    protected static array $properties = [
        'nestedObject'          => TestAliasExporterConvertFalse::class,
    ];

    protected static array $exportAliases = [
        'stringProperty'            => 'externalStringProperty'
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\AliasExporter::class,
    ];
}