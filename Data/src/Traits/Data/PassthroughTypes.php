<?php
namespace Phalconeer\Data\Traits\Data;

trait PassthroughTypes
{
    protected function parseTypes(array $predefinedProperties) : array
    {
        return $predefinedProperties;
    }
}