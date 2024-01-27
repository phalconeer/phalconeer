<?php
namespace Phalconeer\TaskRegistry\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\TaskRegistry as This;

class InitializeFollowUpTasks implements Dto\TransformerStaticInterface
{
    public static function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ): \ArrayObject
    {
        if (!$source->offsetExists('followUpTasks')) {
            $source->offsetSet('followUpTasks', new This\Data\TaskExecutionCollection());
        }
        
        return $source;
    }
}

