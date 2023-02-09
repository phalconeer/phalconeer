<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

trait NotNullExporter
{
    use This\Traits\ConvertedValue;

    public function export(
        bool $preserveKeys = false
    ) : array
    {
        if ($this instanceof Data\CollectionInterface) {
            return $this->convertCollection(false, $preserveKeys);
        }
        return array_reduce(
            $this->properties(),
            function (array $aggregator, $propertyName)
            {
                if (!isset($this->{$propertyName})
                    || is_null($this->{$propertyName})) {
                    return $aggregator;
                }
                $aggregator[$propertyName] = $this->getValue($propertyName);
                return $aggregator;
            },
            []
        );
    }
}