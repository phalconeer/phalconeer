<?php
namespace Phalconeer\Dto\Trait;

trait ArrayLoader
{
    public static function fromArray(array $input) : static
    {
        return new static(new \ArrayObject($input));
    }
}