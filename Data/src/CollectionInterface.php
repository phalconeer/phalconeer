<?php
namespace Phalconeer\Data;

interface CollectionInterface
{
    public function getFieldValues(
        string $fieldName,
        bool $onlyUnique = false,
        bool $preserveKeys = false,
        array $baseArray = []
    ) : array;
}