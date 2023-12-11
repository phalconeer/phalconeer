<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporter extends This\Mock\Test
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\ParseTypes;

    protected static array $properties = [
        'nestedObject'          => TestArrayObjectExporter::class,
    ];

    protected static bool $convertChildren = true;

    protected static bool $preserveKeys = false;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];
}