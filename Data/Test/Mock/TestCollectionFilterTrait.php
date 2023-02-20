<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data\Test as This;
use Phalconeer\Data;

class TestCollectionFilterTrait extends Data\ImmutableCollection
{
    use Data\Trait\Collection\Filter;

  protected string $collectionType = This\Mock\Test::class;
}