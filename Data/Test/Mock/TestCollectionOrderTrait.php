<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data\Test as This;
use Phalconeer\Data;

class TestCollectionOrderTrait extends Data\ImmutableCollection
{
    use Data\Trait\Collection\Order;

  protected string $collectionType = This\Mock\Test::class;
}