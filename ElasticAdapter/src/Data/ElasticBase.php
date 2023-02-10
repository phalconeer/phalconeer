<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Data\Helper\ParseValueHelper as PVH;

class ElasticBase extends Data\ImmutableData
{
    use Dto\Traits\AliasLoader,
        Dto\Traits\AliasExporter;

    protected static array $_internalProperties = [
        '_indexDateField',
    ];

    protected static array $_properties = [
        'id'                    => PVH::TYPE_STRING,
        'index'                 => PVH::TYPE_STRING,
        'sequenceNumber'        => PVH::TYPE_INT,
        'primaryTerm'           => PVH::TYPE_INT,
    ];

    protected static array $_loadAliases = [
        '_id'                   => 'id',
        '_index'                => 'index',
        '_seq_no'               => 'sequenceNumber',
        '_primary_term'         => 'primaryTerm',
    ];

    protected static array $_exportAliases = [
        'index'                 => null,
        'sequenceNumber'        => null,
        'primaryTerm'           => null,
    ];

    protected static array $_loadTransformers = [
        Dto\Helper\TraitsHelper::LOADER_METHOD_ALIAS,
    ];

    protected ?string $_indexDateField;

    protected string $id;

    protected string $index;

    /**
     * Where the document is stored, or to be stored.
     */
    protected int $primaryTerm;

    protected int $sequenceNumber;

    public function getIndexDateValue() : ?\DateTime
    {
        if (is_null($this->_indexDateField)
            || !array_key_exists($this->_indexDateField, $this->_propertiesCache)
            || !$this->{$this->_indexDateField} instanceof \DateTime) {
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

    public function primaryTerm() : ?int
    {
        return $this->getValue('primaryTerm');
    }

    public function sequenceNumber() : ?int
    {
        return $this->getValue('sequenceNumber');
    }

    public function setId(string $id) : self
    {
        return $this->setValueByKey('id', $id);
    }

    public function setIndex(string $index) : self
    {
        return $this->setValueByKey('index', $index);
    }
}