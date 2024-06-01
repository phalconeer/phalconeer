<?php
namespace Phalconeer\AuthMethod\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;

class AddRequestTime implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);

        if (!$source->offsetExists('requestTime')) {
            $source->offsetSet('requestTime', new \DateTime());
        }

        return $source;
    }
}