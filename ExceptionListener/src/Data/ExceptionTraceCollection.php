<?php
namespace Phalconeer\ExceptionListener\Data;

use Phalconeer\Dto;
use Phalconeer\ExceptionListener as This;

class ExceptionTraceCollection extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\ArrayObjectExporter;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected string $collectionType = This\Data\ExceptionTrace::class;
}