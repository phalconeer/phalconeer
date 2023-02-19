<?php
namespace Phalconeer\Data;

use Phalconeer\Data as This;

interface CollectionInterface extends CommonInterface,
                                        \ArrayAccess,
                                        \IteratorAggregate
{
    public function getFieldValues(
        string $fieldName,
        bool $onlyUnique = false,
        bool $preserveKeys = false,
        array $baseArray = []
    ) : array;

    public function getIterator() : \ArrayIterator;

    public function merge(
        This\CollectionInterface $newObject = null,
        bool $ignoreKeys = true
    ) : This\CollectionInterface;
}