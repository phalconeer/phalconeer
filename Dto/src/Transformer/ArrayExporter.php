<?php
namespace Phalconeer\Dto\Traits;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class ArrayExporter implements This\TransformerInterface
{
    use This\Traits\ConvertedValue;

    public function __construct(
        protected bool $convertChildren = true,
        protected bool $preserveKeys = false
    )
    {
    }

    public function transform(
        $source,
        Data\DataInterface $baseObject = null,
        \ArrayObject $parameters = null
    )
    {

    }

    public function toArray(
        bool $convertChildren = null,
        bool $preserveKeys = null
    ) : array
    {
        if (is_null($convertChildren)) {
            $convertChildren = $this->_convertChildren;
        }
        if (is_null($preserveKeys)) {
            $preserveKeys = $this->_preserveKeys;
        }

        if ($this instanceof Data\CollectionInterface) {
            return $this->convertCollection($convertChildren, $preserveKeys);
        }
        return array_reduce(
            $this->properties(),
            function (array $aggregator, $propertyName) use ($convertChildren, $preserveKeys)
            {
                if ($convertChildren) {
                    $aggregator[$propertyName] = $this->getConvertedValue($propertyName, $preserveKeys);
                } else {
                    $aggregator[$propertyName] = $this->getValue($propertyName);
                }
                return $aggregator;
            },
            []
        );
    }
}