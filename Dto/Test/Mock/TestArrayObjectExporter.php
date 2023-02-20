<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporter extends This\Mock\Test
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\Data\ParseTypes;

    protected static array $_properties = [
        'nestedObject'          => TestArrayObjectExporter::class,
    ];

    protected static bool $_convertChildren = true;

    protected static bool $_preserveKeys = false;

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT,
    ];
}