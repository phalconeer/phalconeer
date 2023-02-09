<?php
namespace Phalconeer\Dto\Traits;

trait ArrayLoader
{
    public static function fromArray(array $input)
    {
        return new static(new \ArrayObject($input));
    }
}