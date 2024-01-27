<?php
namespace Phalconeer\TaskRegistry\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class GetDetailObject implements Dto\TransformerStaticInterface
{
    public static function createInstance(
        \ArrayObject | Data\CommonInterface $source,
        string $dataProperty = 'detail',
        string $definitionProperty = 'detailClass'
    ): \ArrayObject
    {
        if ($source->offsetExists($dataProperty)
            && $source->offsetExists($definitionProperty)
            && is_array($source->offsetGet($dataProperty))) {
            $className = $source->offsetGet($definitionProperty);
            $source->offsetSet(
                $dataProperty,
                new $className(
                    new \ArrayObject(
                        $source->offsetGet($dataProperty)
                    )
                )
            );
        }
        
        return $source;
    }

    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        return self::createInstance($source);
    }
}