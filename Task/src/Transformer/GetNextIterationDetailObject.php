<?php
namespace Phalconeer\Task\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Task as This;

class GetNextIterationDetailObject implements Dto\TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        return This\Transformer\GetDetailObject::createInstance(
            $source,
            'nextIterationDetail',
            'nextIterationDetailClass'
        );
    }
}