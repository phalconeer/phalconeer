<?php
namespace Phalconeer\Dto\Trait;

trait ObjectLoader
{
    public static function fromObject(\stdClass $input)
    {
        return static::__construct(new \ArrayObject(get_object_vars($input)));
    }
}