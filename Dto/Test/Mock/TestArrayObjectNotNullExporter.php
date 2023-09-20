<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectNotNullExporter extends This\Mock\TestArrayExporter
{
    use Dto\Trait\ArrayObjectNotNullExporter;

    protected static array $properties = [
        'nestedObject'          => TestArrayObjectNotNullExporter::class,
    ];

    protected static bool $convertChildren = true;

    protected static bool $preserveKeys = false;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT_WITHOUT_NULLS,
    ];
}