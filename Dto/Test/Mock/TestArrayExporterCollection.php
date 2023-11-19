<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterCollection extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\ArrayExporter;

    protected string $collectionType = This\Mock\TestArrayExporter::class;

    protected static array $exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}