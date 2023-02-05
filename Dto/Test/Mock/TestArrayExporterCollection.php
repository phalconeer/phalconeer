<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayExporterCollection extends Data\ImmutableCollection
{
    use Dto\Traits\ArrayExporter;

  protected string $collectionType = This\Mock\TestArrayExporter::class;
}