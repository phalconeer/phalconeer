<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterWithoutParseTypes extends This\Mock\Test
{
    use Dto\Trait\ArrayExporter;

    protected static array $_properties = [
        'nestedObject'          => TestArrayExporterWithoutParseTypes::class,
    ];

    protected static bool $_convertChildren = true;

    protected static bool $_preserveKeys = false;

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}