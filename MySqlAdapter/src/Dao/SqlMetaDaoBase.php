<?php
namespace Phalconeer\MySqlAdapter\Dao;

use Phalconeer\Dao;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\MySqlAdapter as This;

class SqlMetaDaoBase extends SqlDaoBase
{
    const LOOKUP_LIMIT = 1000;

    /**
     * The name of the table the meta data is written.
     */
    protected This\Dao\SqlDaoBase $metaDao;

    /**
     * The name of the table the meta data is written.
     */
    protected string $metaKey = '';

    /**
     * The name of the table the meta data is written.
     */
    protected string $headerClass;

    /**
     * The name of the table the meta data is written.
     */
    protected string $metaClass;

    protected array $headerProperties;

    /**
     * Can be set to help during sorting
     */
    protected array $metaProperties;

    public function __construct(array $connections = [], Dao\DaoReadAndWriteInterface $metaDao)
    {
        parent::__construct($connections);

        $this->metaDao = $metaDao;

        $this->headerProperties = (new $this->headerClass())->properties();
    }

    protected function getMetaFields(Data\ImmutableData $data, Data\ImmutableData $headData) : \ArrayObject
    {
        $return = new \ArrayObject();
        $headerFields = array_keys($headData->properties());
        foreach ($data->properties() as $property => $type) {
            if (!in_array($property, $headerFields)
                && Data\Helper\ParseValueHelper::isSimpleValue($type)) {
                $return->offsetSet($property, $data->$property());
            }
        }

        return $return;
    }

    /**
     * Saves a row into the table. If the ID is present, it updates the row. If not, it inserts one.
     */
    public function save(
        Dto\ArrayObjectExporterInterface $data,
        bool $forceInsert = false,
        string $insertMode = Dao\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : ?Dto\ImmutableDto
    {
        $header = new $this->headerClass($data->export());
        $header = parent::save($header, $forceInsert, $insertMode);
        $primaryKeyValue = $header->getPrimaryKeyValue()[0]; // Meta table headers will always have an auto incremented primary key
        if (empty($header->getPrimaryKeyValue())) {
            return null;
        }
        $data = $data->setValueByKey($data->getPrimaryKey()[0], $primaryKeyValue);
        $metaFields = $this->getMetaFields($data, $header)->getIterator();

        $metaValues = $this->metaDao->getRecords([$this->metaKey => $primaryKeyValue]);
        $existingMetaIds = [];
        if ($metaValues instanceof \ArrayObject) {
            $iterator = $metaValues->getIterator();
            while ($iterator->valid()) {
                $existingMetaIds[$iterator->current()->offsetGet('key')] = $iterator->current()->offsetGet('id');
                $iterator->next();
            }
        }
        while ($metaFields->valid()) {
            if (!empty($metaFields->current())) {
                $meta = new $this->metaClass([
                    'id'                => (array_key_exists($metaFields->key(), $existingMetaIds))
                                            ? $existingMetaIds[$metaFields->key()]
                                            : null,
                    $this->metaKey      => $primaryKeyValue,
                    'key'               => $metaFields->key(),
                    'value'             => $metaFields->current()
                ]);
                $this->metaDao->save($meta);
            } else {
                if (array_key_exists($metaFields->key(), $existingMetaIds)) {
                    $this->metaDao->delete([
                        'id'        => $existingMetaIds[$metaFields->key()]
                    ]);
                }
            }
            $metaFields->next();
        }
        return $data;
    }

    protected function splitConditions(array $whereConditions = []) : array
    {
        $headerKeys = array_keys($this->headerProperties);
        $metaConditions = array_reduce(
            array_keys($whereConditions),
            function ($aggregator, $currentKey) use (&$whereConditions, $headerKeys) {
                if (!in_array($currentKey, $headerKeys)) {
                    $aggregator[] = [
                        'key'       => $currentKey,
                        'value'     => $whereConditions[$currentKey]
                    ];
                    unset($whereConditions[$currentKey]);
                }
                return $aggregator;
            },
            []
            );
        
        return [$whereConditions, $metaConditions];
    }

    protected function filterHeaderOrder(string $orderString) : string
    {
        if (empty($orderString)) {
            return '';
        }
        $headerKeys = array_keys($this->headerProperties);
        $orderStringPieces = explode(',', $orderString);
        $filteredOrder = array_reduce(
            $orderStringPieces,
            function ($aggregator, $orderStringPiece) use ($headerKeys) {
                $field = (substr($orderStringPiece, 0, 1) === '-')
                    ? substr($orderStringPiece, 1)
                    : $orderStringPiece;
                if (!in_array($field, $headerKeys)) {
                    return $aggregator;
                }
                $aggregator[] = $orderStringPiece;
                return $aggregator;
            },
            []
        );

        return implode(',', $filteredOrder);
    }

    protected function getHeaderResults(
        array $whereConditions,
        int $limit = self::LOOKUP_LIMIT,
        int $offset = 0,
        string $orderString = ''
    ) : ?\ArrayObject
    {
        return parent::getRecords(
            $whereConditions,
            0,
            $offset,
            $orderString
        );
    }

    protected function getMetaResultIds(array $whereConditions, int $limit = self::LOOKUP_LIMIT) : array
    {
        $metaResultIds = [];
        foreach ($whereConditions as $condition) {
            $metaResult = $this->metaDao->getRecords($condition, $limit);
            if (is_null($metaResult)) {
                continue;
            }
            $iterator = $metaResult->getIterator();
            while ($iterator->valid()) {
                $metaResultIds[$iterator->current()->offsetGet($this->metaKey)] = true;
                $iterator->next();
            }
        }

        return $metaResultIds;
    }

    protected function getFullResult(\ArrayObject $data) : \ArrayObject
    {
        $metaData = $this->metaDao->getRecords([$this->metaKey => $data->offsetGet('id')], self::LOOKUP_LIMIT);
        $iterator = $metaData->getIterator();
        while ($iterator->valid()) {
            $data->offsetSet($iterator->current()->offsetGet('key'), $iterator->current()->offsetGet('value'));
            $iterator->next();
        }
        return $data;
    }

    /**
     * Returns a record from the table.
     */
    public function getRecord(array $whereConditions = []) : ?\ArrayObject
    {
        [$headerConditions, $metaConditions] = $this->splitConditions($whereConditions);

        if (count($headerConditions) > 0) {
            $headerResults = $this->getHeaderResults($headerConditions);
        }

        $metaResultIds = $this->getMetaResultIds($metaConditions);

        $noHeaderResult = count($headerConditions) > 0 && (is_null($headerResults) || $headerResults->count() === 0);
        $noMetaResult = count($metaConditions) > 0 && (count($metaResultIds) === 0);

        if ($noHeaderResult || $noMetaResult) {
            return null;
        }

        if (count($metaConditions) === 0) {
            return $this->getFullResult($headerResults->offsetGet(0));
        }

        if (count($headerConditions) === 0) {
            return $this->getRecord(['id' => array_keys($metaResultIds)]);
        }

        $iterator = $headerResults->getIterator();
        while ($iterator->valid()) {
            if (array_key_exists($iterator->current()->offsetGet('id'), $metaResultIds)) {
                return $this->getFullResult($iterator->current());
            }
        }
        return null;
    }

    protected function translateTypeToSort(string $type) : int
    {
        switch ($type) {
            case Data\Helper\ParseValueHelper::TYPE_INT:
            case Data\Helper\ParseValueHelper::TYPE_INTEGER:
            case Data\Helper\ParseValueHelper::TYPE_FLOAT:
            case Data\Helper\ParseValueHelper::TYPE_DOUBLE:
            case Data\Helper\ParseValueHelper::TYPE_REAL:
                return SORT_NUMERIC;
            default:
                return SORT_STRING;
        }
    }

    protected function getSortedRecords(
        array $ids,
        int $limit = 0,
        int $offset = 0,
        string $orderString
    )
    {
        $combinedResult = array_map(
            function ($id) {
                return $this->getRecord(['id' => $id])->getArrayCopy();
            },
            array_keys($ids)
        );

        if ($orderString === '') {
            return $this->getResultObjectSet(
                array_slice(
                    $combinedResult,
                    $offset,
                    $limit
                ),
                false
            );
        }

        $orderPieces = explode(',', $orderString);
        $params = array_reduce(
            $orderPieces,
            function ($aggregator, $orderPiece) use ($combinedResult) {
                $property = (substr($orderPiece, 0, 1) === '-') ? substr($orderPiece, 1) : $orderPiece;
                if (array_key_exists($property, $this->headerProperties)) {
                    $sort = $this->translateTypeToSort($this->headerProperties[$property]);
                } else if (array_key_exists($property, $this->metaProperties)) {
                    $sort = $this->translateTypeToSort($this->metaProperties[$property]);
                } else {
                    $sort = SORT_STRING;
                }
                $aggregator[] = array_column($combinedResult, $property);
                $aggregator[] = ($property === $orderPiece) ? SORT_ASC : SORT_DESC;
                $aggregator[] = $sort;
                return $aggregator;
            },
            []
        );
        $params[] = &$combinedResult;
        call_user_func_array('array_multisort', $params);

        return $this->getResultObjectSet(
            array_slice(
                $combinedResult,
                $offset,
                $limit
            ),
            false
        );
    }

    /**
     * Returns all records from the table based on the criteria.
     */
    public function getRecords(
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0,
        string $orderString = ''
    ) : ?\ArrayObject
    {
        [$headerConditions, $metaConditions] = $this->splitConditions($whereConditions);
        $headerResults = null;
        if (count($headerConditions) > 0
            || count($metaConditions) === 0) { //This needs to run if there are conditions for the header or no conditions at all
            $headerResults = $this->getHeaderResults(
                $headerConditions,
                $limit,
                $offset,
                $this->filterHeaderOrder($orderString)
            );
        }

        $metaResultIds = $this->getMetaResultIds($metaConditions, $limit);

        $noHeaderResult = count($headerConditions) > 0 && (is_null($headerResults) || $headerResults->count() === 0);
        $noMetaResult = count($metaConditions) > 0 && (count($metaResultIds) === 0);
        $noResult = (is_null($headerResults) || $headerResults->count() === 0) && count($metaResultIds) === 0;
        if ($noHeaderResult
            || $noMetaResult
            || $noResult ) {
            return null;
        }

        if (count($metaConditions) === 0) {
            $combinedIds = [];
            $iterator = $headerResults->getIterator();
            while ($iterator->valid()) {
                $combinedIds[$iterator->current()->offsetGet('id')] = true;
                $iterator->next();
            }
            return $this->getSortedRecords(
                $combinedIds,
                $limit,
                $offset,
                $orderString
            );
        }

        if (count($headerConditions) === 0) {
            return $this->getSortedRecords(
                $metaResultIds,
                $limit,
                $offset,
                $orderString
            );
        }

        $combinedIds = [];
        $iterator = $headerResults->getIterator();
        while ($iterator->valid()) {
            if (array_key_exists($iterator->current()->offsetGet('id'), $metaResultIds)) {
                $combinedIds[$iterator->current()->offsetGet('id')] = true;
            }
            $iterator->next();
        }
        return count($combinedIds) === 0 ? null : $this->getSortedRecords(
            $combinedIds,
            $limit,
            $offset,
            $orderString
        );
    }

    /**
     * Returns the total count of specific records.
     */
    public function getCount(array $whereConditions = [], int $limit = self::LOOKUP_LIMIT) : int
    {
        $records = $this->getRecords($whereConditions, $limit);

        return (is_null($records)) ? 0 : $records->count();
    }

    /**
     * Deletes a record from the table.
     */
    public function delete(array $whereConditions = []) : bool
    {
        $items = $this->getRecords($whereConditions, self::LOOKUP_LIMIT);
        $iterator = $items->getIterator();
        while($iterator->valid()) {
            if (!$this->metaDao->delete([$this->metaKey => $iterator->current()->offsetGet('id')])) {
                return false;
            }
            if (!parent::delete(['id' => $iterator->current()->offsetGet('id')])) {
                return false;
            }
            $iterator->next();
        }

        return true;
    }
}
