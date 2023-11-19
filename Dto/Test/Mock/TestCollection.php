<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestCollection extends Dto\ImmutableDtoCollection
{
    protected string $collectionType = This\Mock\Test::class;
}