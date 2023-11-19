<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto\Test as This;
use Phalconeer\Dto;

class TestCollectionFilterTrait extends Dto\ImmutableDtoCollection
{
  use Dto\Trait\FilterExporter;

  protected string $collectionType = This\Mock\Test::class;
}