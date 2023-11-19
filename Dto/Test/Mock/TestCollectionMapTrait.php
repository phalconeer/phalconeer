<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto\Test as This;
use Phalconeer\Dto;

class TestCollectionMapTrait extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\MapFieldExporter;

  protected string $collectionType = This\Mock\Test::class;
}