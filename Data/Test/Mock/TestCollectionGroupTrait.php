<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data\Test as This;
use Phalconeer\Data;

class TestCollectionGroupTrait extends Data\ImmutableCollection
{
    use Data\Trait\Collection\Group;

  protected string $collectionType = This\Mock\Test::class;
}