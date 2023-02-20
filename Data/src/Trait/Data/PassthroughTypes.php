<?php
namespace Phalconeer\Data\Trait\Data;

trait PassthroughTypes
{
    protected function parseTypes(array $predefinedProperties) : array
    {
        return $predefinedProperties;
    }
}