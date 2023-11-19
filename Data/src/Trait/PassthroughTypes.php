<?php
namespace Phalconeer\Data\Trait;

trait PassthroughTypes
{
    protected function parseTypes(array $predefinedProperties) : array
    {
        return $predefinedProperties;
    }
}