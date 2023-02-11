<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestCollection extends Dto\ImmutableCollectionDto
{
    protected string $collectionType = This\Mock\Test::class;
}