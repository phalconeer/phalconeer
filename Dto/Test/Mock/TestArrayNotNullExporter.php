<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayNotNullExporter extends This\Mock\TestArrayExporter
{
    use Dto\Traits\ArrayNotNullExporter;

    protected static array $_properties = [
        'nestedObject'          => TestArrayNotNullExporter::class,
    ];
}