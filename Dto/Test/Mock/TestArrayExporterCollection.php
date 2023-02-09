<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterCollection extends Dto\ImmutableCollectionDto
{
    use Dto\Traits\ArrayExporter;

    protected string $collectionType = This\Mock\TestArrayExporter::class;

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY,
    ];
}