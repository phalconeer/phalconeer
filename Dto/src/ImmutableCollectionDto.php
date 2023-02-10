<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ImmutableCollectionDto extends Data\ImmutableCollection
{
    protected static bool $_convertChildren = true;

    protected static array $_exportTransformers = [];

    protected static array $_loadTransformers = [];

    protected static bool $_preserveKeys = false;

    public function exportWithArray(
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
        return $this->exportWithArray(
            static::getExportTransformers(),
            $parameters
        );
    }

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject
    {
        $transformers = static::getLoadTransformers();
        foreach ($transformers as $transformer) {
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

    public static function getConvertChildren() : bool
    {
        return static::$_convertChildren;
    }

    public static function getExportTransformers(array $baseTransformers = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge(
                $parentClassName::getExportTransformers(),
                static::$_exportTransformers,
                $baseTransformers
            )
            : array_merge(static::$_exportTransformers, $baseTransformers);
    }

    public static function getLoadTransformers() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge(
                $parentClassName::getLoadTransformers(),
                ((isset(static::$_loadTransformers)) ? static::$_loadTransformers : []),
            )
            : ((isset(static::$_loadTransformers))
                ? static::$_loadTransformers
                : []);
    }

    public static function getPreserveKeys() : bool
    {
        return static::$_preserveKeys;
    }

    public static function withLoadTransformers(\ArrayObject $inputObject, array $loadTransformers)
    {
        static::$_loadTransformers = $loadTransformers;
        return new static($inputObject);
    }
}