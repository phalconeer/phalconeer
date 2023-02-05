<?php
namespace Phalconeer\Data\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Data\Test as This;

class TestCollection extends Data\ImmutableCollection
{
    protected string $collectionType = This\Mock\Test::class;
}