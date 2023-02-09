<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayObjectExporterCollection extends Data\ImmutableCollection
{
    use Dto\Traits\ArrayObjectExporter;

  protected string $collectionType = This\Mock\ArrayObjectExporter::class;
}