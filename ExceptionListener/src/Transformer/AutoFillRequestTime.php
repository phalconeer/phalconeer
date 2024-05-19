<?php
namespace Phalconeer\ExceptionListener\Transformer;

use PHalconeer\Data;
use PHalconeer\Dto;

class AutoFillRequestTime implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);

        if (!$source->offsetExists('requestTime')) {
            $source->offsetSet(
                'requestTime',
                new \DateTime()
            );
        }

        return $source;
    }
}