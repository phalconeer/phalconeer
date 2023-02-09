<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporter extends This\Mock\Test
{
    use Dto\Traits\ArrayObjectExporter,
        Data\Traits\Data\ParseTypes;

    protected static array $_properties = [
        'nestedObject'          => TestArrayObjectExporter::class,
    ];
}