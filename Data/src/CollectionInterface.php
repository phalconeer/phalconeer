<?php
namespace Phalconeer\Data;

interface CollectionInterface extends CommonInterface
{
    public function getFieldValues(
        string $fieldName,
        bool $onlyUnique = false,
        bool $preserveKeys = false,
        array $baseArray = []
    ) : array;
}