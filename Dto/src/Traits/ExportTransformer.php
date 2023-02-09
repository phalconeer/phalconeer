<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Dto as This;

trait ExportTransformer
{
    /**
     * Recursive loads defined properties crawling up the inheritence tree
     */
    public static function getExportTransformers(array $baseTransformers = []) : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge(
                $parentClassName::getExportTransformers(),
                ((isset(static::$_exportTransformers)) ? static::$_exportTransformers : []),
                $baseTransformers
            )
            : ((isset(static::$_exportTransformers))
                ? array_merge(static::$_exportTransformers, $baseTransformers)
                : $baseTransformers);
    }

    public function exportWithArray(
        array $transformers = [],
        \ArrayObject $parameters = null
    )
    {
        $result = $this;
        $transformers = static::getExportTransformers($transformers);
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
            [],
            $parameters
        );
    }
}