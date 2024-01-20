<?php
namespace Phalconeer\Dto\Trait;

trait ArrayLoader
{
    public static function fromArray(array $input = null) : static
    {
        if (is_null($input)) {
            $input = [];
        }
        return new static(new \ArrayObject($input));
    }
}