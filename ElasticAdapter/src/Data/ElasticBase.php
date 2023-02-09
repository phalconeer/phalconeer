<?php
namespace Phalconeer\ElasticAdapter\Data;

use ArrayObject;
use Phalconeer\Data;
use DateTime;
use Phalconeer\Data\Helper\ParseValueHelper as PVH;

class ElasticBase extends Data\ImmutableData
{
    use Data\Traits\Data\ParseTypes,
        MaskableTrait;

    protected static array $_internalProperties = [
        '_indexDateField',
    ];

    protected static array $_properties = [
        'id'                    => PVH::TYPE_STRING,
        'index'                 => PVH::TYPE_STRING,
        'sequenceNumber'        => PVH::TYPE_INT,
        'primaryTerm'           => PVH::TYPE_INT,
    ];

    protected static array $_keyAliases = [
        'id'                    => '_id',
        'index'                 => '_index',
        'sequenceNumber'        => '_seq_no',
        'primaryTerm'           => '_primary_term',
    ];

    protected ?string $_indexDateField;

    protected string $id;

    protected string $index;

    protected int $sequenceNumber;

    /**
     * Where the document is stored, or to be stored.
     */
    protected int $primaryTerm;

    /**
     * Any additional data formatting required for the obejct can be included here.
     *
     * @param \ArrayObject $input
     * @return \ArrayObject
     */
    protected function convertData(ArrayObject $inputObject) : ArrayObject 
    {
        foreach (static::getKeyAliases() as $newField => $oldField) {
            if ($inputObject->offsetExists($oldField)) {
                $inputObject->offsetSet($newField, $inputObject->offsetGet($oldField));
                $inputObject->offsetUnset($oldField);
            }
        }

        return $inputObject;
    }

    public function getIndexDateValue() : ?DateTime
    {
        if (is_null($this->_indexDateField)
            || !array_key_exists($this->_indexDateField, $this->_propertiesCache)
            || !$this->{$this->_indexDateField} instanceof DateTime) {
            return null;
        }

        return clone $this->{$this->_indexDateField};
    }

    public function getPrimaryKey() : array
    {
        return ['id'];
    }

    public function id() : ?string
    {
        return $this->getValue('id');
    }

    public function setId(string $id) : self
    {
        return $this->setKeyValue('id', $id);
    }

    public function setIndex(string $index) : self
    {
        return $this->setKeyValue('index', $index);
    }
}