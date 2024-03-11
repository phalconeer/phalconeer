<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class GroupExporter implements This\TransformerVariableInterface
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

        return self::groupByField(
            $source,
            $this->groupBy
        );
    }

    public static function groupByField(
        \ArrayObject | Data\CollectionInterface $source,
        string $groupBy
    ) : \ArrayObject
    {
        $iterator = $source->getIterator();
        $className = get_class($source);
        $grouped = new \ArrayObject();

        while ($iterator->valid()) {
            $groupHandler = $iterator->current()->$groupBy();
            if (!is_array($groupHandler)
                && !is_object($groupHandler)) {
                if (!$grouped->offsetExists($groupHandler)) {
                    $grouped->offsetSet($groupHandler, new $className());
                }
                $grouped->offsetGet($groupHandler)->offsetSet(null, $iterator->current());
            }
            $iterator->next();
        }

        return $grouped;
    }
}