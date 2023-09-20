<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterWithoutParseTypes extends This\Mock\Test
{
    use Dto\Trait\ArrayExporter;

    protected static array $properties = [
        'nestedObject'          => TestArrayExporterWithoutParseTypes::class,
    ];

    protected static bool $convertChildren = true;

    protected static bool $preserveKeys = false;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}