<?php
namespace Phalconeer\Data;

use Phalconeer\Exception;
use Phalconeer\Data as This;

abstract class ImmutableCollection implements This\DataInterface, \ArrayAccess
{
    protected string $collectionType;

    protected \ArrayObject $collection;

    public function __construct(array $data = null, \ArrayObject $dataObject = null)
    {
        if (is_null($dataObject)) {
            if (is_null($data)) {
                $data = [];
            }
            $dataObject = new \ArrayObject($data);
        }
        $this->collection = new \ArrayObject();

        $iterator = $dataObject->getIterator();
        while ($iterator->valid()) {
            try {
                $this->collection->offsetSet($iterator->key(), $this->parseComplexType($iterator->current()));
            } catch (Exception\TypeMismatchException $exception) {
                throw new Exception\TypeMismatchException(
                    $exception->getMessage() . ' @ item  ' . $iterator->key(),
                    $exception->getCode(),
                    $exception,
                );
            }
            $iterator->next();
        }
    }

    private function parseComplexType($value)
    {
        if (is_array($value)) {
            return new $this->collectionType($value);
        }

        if ($value instanceof \ArrayObject) {
            return new $this->collectionType(null, $value);
        }

        if ($value instanceof \stdClass) {
            return new $this->collectionType(get_object_vars($value));
        }

        if (!is_object($value)
            || !is_a($value, $this->collectionType)) {
            throw new Exception\TypeMismatchException('Expected class `' . $this->collectionType . '`or array, received: ' . get_class($value), This\Helper\ExceptionHelper::TYPE_MISMATCH);
        }

        return $value;
    }

    public function properties() : array
    {
        if (!is_null($this->collection)) {
            $iterator = $this->collection->getIterator();
            return $iterator->current()->properties();
        }

        $dummyObject = new $this->collectionType();
        return $dummyObject->properties();
    }

    public function offsetGet($offset) : ?This\DataInterface
    {
        return ($this->collection->offsetExists($offset)) ? clone($this->collection->offsetGet($offset)) : null;
    }

    public function offsetExists($offset) : bool
    {
        if (is_null($offset)) {
            return false;
        }
        return $this->collection->offsetExists($offset);
    }

    public function offsetSet($offset, $value) : void
    {
        $this->collection->offsetSet($offset, $this->parseComplexType($value));
    }

    public function offsetUnset($offset) : void
    {
        $this->collection->offsetUnset($offset);
    }

    public function count() : int
    {
        return count($this->collection);
    }

    public function getIterator() : \ArrayIterator
    {
        return $this->collection->getIterator();
    }

    public function getFieldValues(
        string $fieldName,
        bool $onlyUnique = false,
        bool $preserveKeys = false,
        array $baseArray = []
    ) : array
    {
        $iterator = $this->getIterator();

        while ($iterator->valid()) {
            $value = $iterator->current()->{$fieldName}();
            if (!$onlyUnique
                || !in_array($value, $baseArray)) {
                if ($preserveKeys) {
                    $baseArray[$iterator->key()] = $value;
                } else {
                    $baseArray[] = $value;
                }
            }
            $iterator->next();
        }

        return $baseArray;
    }
}