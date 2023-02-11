<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectNotNullExporter extends This\Mock\TestArrayExporter
{
    use Dto\Traits\ArrayObjectNotNullExporter;

    protected static array $_properties = [
        'nestedObject'          => TestArrayObjectNotNullExporter::class,
    ];

    protected static bool $_convertChildren = true;

    protected static bool $_preserveKeys = false;

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT_WITHOUT_NULLS,
    ];
}