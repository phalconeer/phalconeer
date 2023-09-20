<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayNotNullExporter extends This\Mock\TestArrayExporter
{
    use Dto\Trait\ArrayNotNullExporter;

    protected static array $properties = [
        'nestedObject'          => TestArrayNotNullExporter::class,
    ];

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_WITHOUT_NULLS,
    ];
}