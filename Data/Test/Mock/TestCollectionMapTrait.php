<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data\Test as This;
use Phalconeer\Data;

class TestCollectionMapTrait extends Data\ImmutableCollection
{
    use Data\Traits\Collection\Map;

  protected string $collectionType = This\Mock\Test::class;
}