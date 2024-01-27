<?php
namespace Phalconeer\TaskRegistry\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class GetExecutionTime implements Dto\TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        if (!$source->offsetExists('executionTime')) {
            $source->offsetSet('executionTime', microtime(true) - START_TIME);
        }
        
        return $source;
    }
}