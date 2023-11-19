<?php
namespace Phalconeer\Data;

use Phalconeer\Exception;
use Phalconeer\Data as This;

abstract class ImmutableCollection implements This\CollectionInterface
{
    protected \ArrayObject $collection;

    protected ?This\CollectionMetaInterface $collectionMeta;

    protected string $collectionType;

    public function __construct(\ArrayObject $dataObject = null)
    {
        if (!isset($this->collectionMeta)
            || is_null($this->collectionMeta)) {
            $this->collectionMeta = new This\CollectionMeta();
        }
        if (is_null($dataObject)) {
            $dataObject = new \ArrayObject();
        }
        $this->collection = new \ArrayObject();
        $dataObject = $this->initializeData($dataObject);

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

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject
    {
        return $inputObject;
    }

    protected function parseComplexType($value)
    {
        if (is_array($value)) {
            return new $this->collectionType(new \ArrayObject($value));
        }

        if ($value instanceof \ArrayObject) {
            return new $this->collectionType($value);
        }

        if ($value instanceof \stdClass) {
            return new $this->collectionType(new \ArrayObject(get_object_vars($value)));
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

    public function propertyTypes() : array
    {
        if (!is_null($this->collection)) {
            $iterator = $this->collection->getIterator();
            return $iterator->current()->propertyTypes();
        }

        $dummyObject = new $this->collectionType();
        return $dummyObject->propertyTypes();
    }

    public function offsetGet($offset) : ?This\CommonInterface
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

    public function offsetSet(
        $offset,
        $value,
        bool $isSilent = false
    ) : void
    {
        $this->collection->offsetSet($offset, $this->parseComplexType($value));
        if (!$isSilent) {
            $this->collectionMeta->setDirty(true);
        }
    }

    public function offsetUnset($offset, bool $isSilent = false) : void
    {
        $this->collection->offsetUnset($offset);
        if (!$isSilent) {
            $this->collectionMeta->setDirty(true);
        }
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

    public function merge(
        This\CollectionInterface $newObject = null,
        bool $ignoreKeys = true,
        bool $isSilent = false
    ) : This\CollectionInterface
    {
        if (is_null($newObject)) {
            return $this;
        }
        $iterator = $newObject->getIterator();
        $changed = false;
        while ($iterator->valid()) {
            $key = ($ignoreKeys)
                ? null
                : implode(
                    '.',
                    $iterator->current()->getPrimaryKeyValue()
                );
            $this->collection->offsetSet($key, $iterator->current());
            $changed = true;
            $iterator->next();
        }
        if ($changed
            && !$isSilent) {
            $this->collectionMeta->setDirty(true);
        }
        return $this;
    }
}