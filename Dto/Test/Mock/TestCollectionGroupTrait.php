<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto\Test as This;
use Phalconeer\Dto;

class TestCollectionGroupTrait extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\GroupExporter;

  protected string $collectionType = This\Mock\Test::class;
}