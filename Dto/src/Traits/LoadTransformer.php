<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Dto as This;

trait LoadTransformer
{
    /**
     * Recursive loads defined properties crawling up the inheritence tree
     */
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

    public static function withLoadTransformers(\ArrayObject $inputObject, array $loadTransformers)
    {
        static::$_loadTransformers = $loadTransformers;
        return new static($inputObject);
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
}