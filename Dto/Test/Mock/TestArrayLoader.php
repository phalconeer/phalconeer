<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestArrayLoader extends This\Mock\Test
{
    use Dto\Trait\ArrayLoader;

    protected static array $_properties = [
        'nestedObject'          => TestArrayLoader::class,
    ];
}