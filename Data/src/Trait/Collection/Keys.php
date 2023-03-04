<?php
namespace Phalconeer\Data\Trait\Collection;

trait Keys
{
    public function getKeys() : array
    {
        $result = [];
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $result[] = $iterator->key();
            $iterator->next();
        }

        return $result;
    }
}