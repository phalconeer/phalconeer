<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto\Test as This;
use Phalconeer\Dto;

class TestCollectionOrderTrait extends Dto\ImmutableDtoCollection
{
    use Dto\Trait\OrderExporter;

  protected string $collectionType = This\Mock\Test::class;
}