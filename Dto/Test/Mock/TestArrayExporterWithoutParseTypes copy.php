<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterWithoutParseTypes extends This\Mock\Test
{
    use Dto\Traits\ArrayExporter;

    protected static array $_properties = [
        'nestedObject'          => TestArrayExporterWithoutParseTypes::class,
    ];
}