<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporterWithoutParseTypes extends This\Mock\Test
{
    use Dto\Traits\ArrayObjectExporter;

    protected static array $_properties = [
        'nestedObject'          => TestArrayObjectExporterWithoutParseTypes::class,
    ];

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT,
    ];
}