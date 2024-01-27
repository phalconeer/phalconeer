<?php
namespace Phalconeer\Dto\Transformer;

class ArrayLoader
{
    const AUTO_CONVERT_METHOD = 'autoConvertChildren';

    public static function autoConvertChildren(\ArrayObject $inputObject) : \ArrayObject
    {
        $iterator = $inputObject->getIterator();
        while ($iterator->valid()) {
            if (is_array($iterator->current())) {
                $inputObject->offsetSet(
                    $iterator->key(),
                    new \ArrayObject($iterator->current())
                );
            }
            $iterator->next();
        }

        return $inputObject;
    }
}