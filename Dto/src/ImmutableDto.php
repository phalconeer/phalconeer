<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

abstract class ImmutableDto extends Data\ImmutableData implements This\DtoExporterInterface
{
    public Data\MetaInterface $meta;

    public ?This\TransformerMetaInterface $transformer;

    protected static bool $convertChildren = true;

    protected static array $exportAliases = [];

    protected static array $exportTransformers = [];

    protected static array $loadAliases = [];

    protected static array $loadTransformers = [];

    protected static bool $preserveKeys = false;

    public function __construct(
        \ArrayObject $inputObject = null,
        array $loadTransformers = null,
        array $loadAliases = null
    )
    {
        if (!isset($this->transformer)
            || is_null($this->transformer)) {
            $this->transformer = new TransfromerMeta();
        }
        $this->transformer->setConvertChildren(static::$convertChildren);
        $this->transformer->setPreserveKeys(static::$preserveKeys);
        $this->transformer->setExportAliases(self::getExportAliases());
        $this->transformer->setExportTransformers(self::getExportTransformers());
        $this->transformer->setLoadAliases($loadAliases ?? self::getLoadAliases());
        $this->transformer->setLoadTransformers($loadTransformers ?? self::getLoadTransformers());
        parent::__construct($inputObject);
    }

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

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject
    {
        foreach ($this->transformer->loadTransformers() as $transformer) {
            if (is_string($transformer)
                && is_callable([$this, $transformer])) {
                $inputObject = call_user_func_array([$this, $transformer], [$inputObject, $this]);
            }
            if (is_object($transformer)
                && $transformer instanceof This\TransformerInterface) {
                $inputObject = $transformer->transform($inputObject, $this);
            }
            if (is_array($transformer)
                && is_callable($transformer)) {
                $inputObject = call_user_func_array($transformer, [$inputObject, $this]);
            }
            if (is_callable([$transformer, 'transform'])) {
                $inputObject = call_user_func_array([$transformer, 'transform'], [$inputObject, $this]);
            }
        }
        return $inputObject;
    }

    public function getConvertChildren() : bool
    {
        return $this->transformer->convertChildren();
    }

    public static function getExportAliases() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge(
                $parentClassName::getExportAliases(), static::$exportAliases) : 
            static::$exportAliases;
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

    public static function getLoadAliases() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__)) ? 
            array_merge(
                $parentClassName::getLoadAliases(), static::$loadAliases) : 
            static::$loadAliases;
    }

    public static function getLoadTransformers() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge(
                $parentClassName::getLoadTransformers(),
                ((isset(static::$loadTransformers)) ? static::$loadTransformers : []),
            )
            : ((isset(static::$loadTransformers))
                ? static::$loadTransformers
                : []);
    }

    public function getPreserveKeys() : bool
    {
        return $this->transformer->preserveKeys();
    }

    public function setExportTransformers(array $exportTransformers) : self
    {
        $this->transformer->setExportTransformers($exportTransformers);
        return $this;
    } 

    public function appendExportTransformers(array $exportTransformers) : self
    {
        $this->transformer->appendExportTransformers($exportTransformers);
        return $this;
    } 

    public function prependExportTransformers(array $exportTransformers) : self
    {
        $this->transformer->prependExportTransformers($exportTransformers);
        return $this;
    } 

    public function setExportAliases(array $exportAliases) : self
    {
        $this->transformer->setExportAliases($exportAliases);
        return $this;
    } 

    public function addExportAliases(array $exportAliases) : self
    {
        $this->transformer->addExportAliases($exportAliases);
        return $this;
    }

    public function setTransformer(This\TransformerMetaInterface $transformer) : self
    {
        $this->transformer = $transformer;
        return $this;
    }
}