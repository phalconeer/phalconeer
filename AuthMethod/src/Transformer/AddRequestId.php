<?php
namespace Phalconeer\AuthMethod\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Id;

class AddRequestId implements Dto\TransformerStaticInterface
{
    public static function transformStatic(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        $source = Dto\Transformer\ArrayObjectExporter::normalizeArrayObject($source);

        if (!$source->offsetExists('requestId')) {
            $source->offsetSet('requestId', Id\Helper\IdHelper::getUuidv4());
        }

        return $source;
    }
}