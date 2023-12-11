<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporterWithoutParseTypes extends This\Mock\Test
{
    use Dto\Trait\ArrayObjectExporter;

    protected static array $properties = [
        'nestedObject'          => TestArrayObjectExporterWithoutParseTypes::class,
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];
}