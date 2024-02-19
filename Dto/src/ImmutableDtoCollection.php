<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;
use Phalconeer\Exception;

class ImmutableDtoCollection extends Data\ImmutableCollection implements This\DtoExporterInterface
{
    public ?Data\CollectionMetaInterface $collectionMeta;

    public ?This\TransformerMetaInterface $transformer;

    protected static bool $convertChildren = true;

    protected static array $exportTransformers = [];

    protected static bool $preserveKeys = false;

    public function __construct(
        \ArrayObject $inputObject = null,
        protected ?array $loadTransformers = null, // This is passed on to the individual objects
        protected ?array $loadAliases = null // This is passed on to the individual objects
    )
    {
        if (!isset($this->transformer)
            || is_null($this->transformer)) {
            $this->transformer = new This\TransformerMeta();
        }
        $this->transformer->setConvertChildren(static::$convertChildren);
        $this->transformer->setPreserveKeys(static::$preserveKeys);
        $this->transformer->setExportTransformers(self::getExportTransformers());
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
            $iterator->current()->transformer->setExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function appendExportTransformers(array $exportTransformers) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->transformer->appendExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function prependExportTransformers(array $exportTransformers) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->transformer->prependExportTransformers($exportTransformers);
            $iterator->next();
        }
        return $this;
    } 

    public function setExportAliases(array $exportAliases) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->transformer->setExportAliases($exportAliases);
            $iterator->next();
        }
        return $this;
    } 

    public function addExportAliases(array $exportAliases) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->transformer->addExportAliases($exportAliases);
            $iterator->next();
        }
        return $this;
    }

    public function setCollectionTransformer(This\TransformerMetaInterface $collectionTransformer) : self
    {
        $this->transformer->setConvertChildren($collectionTransformer->convertChildren())
            ->setExportTransformers($collectionTransformer->exportTransformers())
            ->setPreserveKeys($collectionTransformer->preserveKeys());
        return $this;
    }

    public function setTransformer(This\TransformerMetaInterface $transformer) : self
    {
        $iterator = $this->collection->getIterator();
        while ($iterator->valid()) {
            // Only set DTO parameters
            $iterator->current()->transformer->setConvertChildren($transformer->convertChildren())
                ->setExportAliases($transformer->exportAliases())
                ->setExportTransformers($transformer->exportTransformers())
                ->setPreserveKeys($transformer->preserveKeys());
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
            $this->transformer->exportTransformers(),
            $parameters
        );
    }
}