<?php
namespace Phalconeer\Dto;

use ArrayObject;
use Phalconeer\Data;
use Phalconeer\Dto as This;
use Phalconeer\Exception;

class ImmutableDtoCollection extends Data\ImmutableCollection
{
    /**
    * @var Data\CollectionMetaInterface & This\DtoCollectionMetaInterface
    */
    public ?Data\CollectionMetaInterface $collectionMeta;

    protected static bool $convertChildren = true;

    protected static array $exportTransformers = [];

    protected static bool $preserveKeys = false;

    public function __construct(
        \ArrayObject $inputObject = null,
        protected ?array $loadTransformers = null,
        protected ?array $loadAliases = null
    )
    {
        if (!isset($this->collectionMeta)
            || is_null($this->collectionMeta)) {
            $this->collectionMeta = new This\DtoCollectionMeta();
        }
        $this->collectionMeta->setConvertChildren(static::$convertChildren);
        $this->collectionMeta->setPreserveKeys(static::$preserveKeys);
        $this->collectionMeta->setExportTransformers(self::getExportTransformers());
        parent::__construct($inputObject);
    }

    public static function getExportTransformers(array $baseTransformers = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge(
                $parentClassName::getExportTransformers(),
                static::$exportTransformers,
                $baseTransformers
            )
            : array_merge(static::$exportTransformers, $baseTransformers);
    }

    protected function parseComplexType($value)
    {
        if (is_array($value)) {
            return new $this->collectionType(
                new \ArrayObject($value),
                $this->loadTransformers,
                $this->loadAliases,
            );
        }

        if ($value instanceof \ArrayObject) {
            return new $this->collectionType(
                $value,
                $this->loadTransformers,
                $this->loadAliases,
            );
        }

        if ($value instanceof \stdClass) {
            return new $this->collectionType(
                new \ArrayObject(get_object_vars($value)),
                $this->loadTransformers,
                $this->loadAliases,
            );
        }

        if (!is_object($value)
            || !is_a($value, $this->collectionType)) {
            throw new Exception\TypeMismatchException('Expected class `' . $this->collectionType . '`or array, received: ' . get_class($value), Data\Helper\ExceptionHelper::TYPE_MISMATCH);
        }

        return $value;
    }


    public function setExportTransformers(array $exportTransformers) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->meta->setExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function appendExportTransformers(array $exportTransformers) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->meta->appendExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function prependExportTransformers(array $exportTransformers) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->meta->prependExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function setExportAliases(array $exportAliases) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->meta->setExportAliases($exportAliases);
            $iterator->next();
        }
        return $this;
    } 

    public function addExportAliases(array $exportAliases) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->meta->addExportAliases($exportAliases);
            $iterator->next();
        }
        return $this;
    }

    public function setCollectionMeta(This\DtoCollectionMetaInterface $collectionMeta) : self
    {
        $this->collectionMeta = $collectionMeta;
        return $this;
    }

    public function setMeta(This\DtoMetaInterface $meta) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->setMeta($meta);
            $iterator->next();
        }
        return $this;
    }

    // public function export(\ArrayObject $parameters = null)
    // {
    //     $result = new ArrayObject();
    //     $iterator = $this->collection->getIterator();
    //     while ($iterator->valid()) {
    //         $result->offsetSet(
    //             null,
    //             $iterator->current()->export()
    //         );
    //         $iterator->next();
    //     }
    //     return $result;
    // }

    public function exportWithTransformers(
        array $transformers = [],
        \ArrayObject $parameters = null
    )
    {
        $result = $this;
        foreach ($transformers as $transformer) {
            if (is_string($transformer)
                && is_callable([$this, $transformer])) {
                $result = call_user_func_array([$this, $transformer], [$result, $this, $parameters]);
            }
            if (is_object($transformer)
                && $transformer instanceof This\TransformerInterface) {
                $result = $transformer->transform($result, $this, $parameters);
            }
            if (is_callable([$transformer, 'transform'])) {
                $result = call_user_func_array([$transformer, 'transform'], [$result, $this, $parameters]);
            }
        }
        return $result;
    }

    public function export(\ArrayObject $parameters = null)
    {
        return $this->exportWithTransformers(
            $this->collectionMeta->exportTransformers(),
            $parameters
        );
    }
}