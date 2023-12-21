<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalconeer\Dto;
use Phalconeer\Data\Helper\ParseValueHelper as PVH;

class ElasticBase extends Dto\ImmutableDto implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\AliasLoader,
        Dto\Trait\AliasExporter;

    const INDEX_DATE_FIELD = '';

    protected static array $properties = [
        'id'                    => PVH::TYPE_STRING,
        'index'                 => PVH::TYPE_STRING,
        'sequenceNumber'        => PVH::TYPE_INT,
        'primaryTerm'           => PVH::TYPE_INT,
    ];

    protected static array $loadAliases = [
        '_id'                   => 'id',
        '_index'                => 'index',
        '_seq_no'               => 'sequenceNumber',
        '_primary_term'         => 'primaryTerm',
    ];

    protected static array $exportAliases = [
        'index'                 => null,
        'sequenceNumber'        => null,
        'primaryTerm'           => null,
    ];

    protected static array $loadTransformers = [
        Dto\Transformer\AliasLoader::TRAIT_METHOD,
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\AliasExporter::TRAIT_METHOD,
    ];

    protected string $id;

    protected string $index;

    /**
     * Where the document is stored, or to be stored.
     */
    protected int $primaryTerm;

    protected int $sequenceNumber;

    public function getIndexDateValue() : ?\DateTime
    {
        if (is_null(static::INDEX_DATE_FIELD)
            || !$this->dataMeta->doesPropertyExist(static::INDEX_DATE_FIELD)
            || !$this->{static::INDEX_DATE_FIELD} instanceof \DateTime) {
            return null;
        }

        return clone $this->{static::INDEX_DATE_FIELD};
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