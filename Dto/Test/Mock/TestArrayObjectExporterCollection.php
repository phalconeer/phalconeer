<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporterCollection extends This\Mock\TestCollection
{
    use Dto\Trait\ArrayObjectExporter;

    protected string $collectionType = This\Mock\TestArrayObjectExporter::class;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];
}