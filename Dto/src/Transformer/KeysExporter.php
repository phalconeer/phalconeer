<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class KeysExporter implements This\TransformerInterface
{
    public function __construct(public string $groupBy)
    {
    }

    public function transform(
        \ArrayObject | Data\CommonInterface $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    ) : \ArrayObject
    {
        if (!$baseObject instanceof This\ImmutableDtoCollection) {
            return $source;
        }

        return self::getKeys(
            $source,
        );
    }

    public static function getKeys(
        \ArrayObject | Data\CollectionInterface $source,
    ) : array
    {
        $result = [];
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            $result[] = $iterator->key();
            $iterator->next();
        }

        return $result;
    }
}