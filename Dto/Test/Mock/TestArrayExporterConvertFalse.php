<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterConvertFalse extends This\Mock\Test
{
    use Dto\Traits\ArrayExporter,
        Data\Traits\Data\ParseTypes;

    protected static array $_properties = [
        'nestedObject'          => TestArrayExporterConvertFalse::class,
    ];

    protected static bool $_convertChildren = false;

    protected static bool $_preserveKeys = false;

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}