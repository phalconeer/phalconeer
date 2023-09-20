<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterConvertFalse extends This\Mock\Test
{
    use Dto\Trait\ArrayExporter,
        Data\Trait\Data\ParseTypes;

    protected static array $properties = [
        'nestedObject'          => TestArrayExporterConvertFalse::class,
    ];

    protected static bool $convertChildren = false;

    protected static bool $preserveKeys = false;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}