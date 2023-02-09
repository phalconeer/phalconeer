<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Data;
use Phalconeer\Dto\Test as This;

class TestCollection extends Data\ImmutableCollection
{
    protected string $collectionType = This\Mock\Test::class;
}