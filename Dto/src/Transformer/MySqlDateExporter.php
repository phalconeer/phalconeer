<?php
namespace Phalconeer\Dto\Transformer;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class MySqlDateExporter implements This\TransformerInterface
{

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
        return self::exportMySqlDate($source);
    }

    public static function exportMySqlDate(
        \ArrayObject $source
    ) : \ArrayObject 
    {
        $iterator = $source->getIterator();
        while ($iterator->valid()) {
            if ($iterator->current() instanceof \DateTime) {
                $source->offsetSet(
                    $iterator->key(),
                    $iterator->current()->format('Y-m-d H:i:s')
                );
            }
            $iterator->next();
        }
        return $source;
    }
}