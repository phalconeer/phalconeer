<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class MySqlDateExporter implements This\TransformerInterface
{
    const TRAIT_METHOD = 'exportAllMySqlDate';

    public function transform(
        $source,
        Data\CommonInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {
        if (is_array($source)) {
            $source = new \ArrayObject($source);
        }
        if (!$source instanceof \ArrayObject) {
            return $source;
        }
        return self::exportAllMySqlDate($source);
    }

    public static function exportAllMySqlDate(
        \ArrayObject $source
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            if ($iterator->current() instanceof \DateTime) {
                $source->offsetSet(
                    $iterator->key(),
                    self::exportMySqlDate($iterator->current())
                );
            }
            $iterator->next();
        }
        return $source;
    }

    public static function exportMySqlDate(
        \DateTime $date
    ) : string 
    {
        return $date->format('Y-m-d H:i:s');
    }
}