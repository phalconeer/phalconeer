<?php
namespace Phalconeer\Dto\Trait;

trait ArrayLoader
{
    public static function fromArray(array $input)
    {
        return new static(new \ArrayObject($input));
    }
}