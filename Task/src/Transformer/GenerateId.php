<?php
namespace Phalconeer\Task\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Id;
use Phalconeer\Task as This;

class GenerateId implements Dto\TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        if (!$source->offsetExists('id')
            || empty($source->offsetGet('id'))) {
            $source->offsetSet('id', Id\Helper\IdHelper::generateWithDayPrefix(This\Helper\TaskHelper::TASK_UNIQUE_ID_LENGTH));
        }
        
        return $source;
    }
}