<?php
namespace Phalconeer\Task\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class RemoveExecutionDetail implements Dto\TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        $source->offsetSet('executionDetail', null);
        
        return $source;
    }
}