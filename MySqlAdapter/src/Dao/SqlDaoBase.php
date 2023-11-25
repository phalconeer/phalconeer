<?php
namespace Phalconeer\MySqlAdapter\Dao;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Dao;
use Phalconeer\MySqlAdapter as This;

class SqlDaoBase extends Dao\DaoBase implements Dao\DaoReadAndWriteInterface
{
    /**
     * The name of the table the dao belongs to.
     */
    protected string $tableName;

    /**
     * Information baout the last error.
     */
    protected string $lastErrorInfo = '';

    public function __construct(array $connections = [])
    {
        parent::__construct($connections);

        if (!isset($this->tableName)) {
            $this->tableName = $this->extractTableName();
        }
    }

    /**
     * Extracts the table name from the calledClassName
     */
    private function extractTableName() : string
    {
        $tempTableName = explode('_', strtolower(
                        preg_replace(array('/^X/', '/dao$/i', '/([A-Z])/'), array('', '', strtolower('_\\1')), $this->calledClassName)
        ));

        array_shift($tempTableName);
        return implode('_', $tempTableName);
    }

    /**
     * Saves a row into the table. If the ID is present, it updates the row. If not, it inserts one.
     */
    public function save(
        Data\DataInterface & Dto\ArrayObjectExporterInterface $data,
        bool $forceInsert = false,
        string $insertMode = Dao\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : ?Dto\ImmutableDto
    {
        $connection = $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE);
        $newRecord = !$data->isStored() || $forceInsert;
        [$fields, $parameters] = This\Helper\SqlQueryHelper::createFieldsAndParameters($data, $newRecord);
        $primaryKey = $data->getPrimaryKey();

        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/';
        $query .= ($newRecord === true)
                ? This\Helper\SqlQueryHelper::getInsertQuery(
                    $this->tableName,
                    $fields,
                    $parameters,
                    $insertMode
                )
                : This\Helper\SqlQueryHelper::getUpdateQuery(
                    $this->tableName,
                    $fields,
                    $parameters,
                    array_reduce(
                        $primaryKey,
                        function ($aggregator, $primaryField) use ($data) {
                            $aggregator[$primaryField] = $data->$primaryField();
                            return $aggregator;
                        }
                    )
                );
    // echo \Phalconeer\Dev\TVarDumper::dump([$query, $fields, $parameters, $data]);
        $stmt = $connection->prepare($query);
    // echo \Phalconeer\Helper\TVarDumper::dump([$stmt, $fields, $parameters, $data]);
        This\Helper\SqlQueryHelper::bindValuesToStatement($stmt, $data->export(), $parameters);
        if (!$newRecord) {
            foreach ($primaryKey as $primaryField) {
                // 0 is needed as index, as the standard whereCondition generator thinks in arrays
                // and 0 is fixed as an update command can not have more than on primary key value
                This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $primaryField . '0', $data->$primaryField());
            }
        }
        $stmt->execute();
        if ($stmt->errorCode() === '00000') { //TODO: check if changeing this from rowCount === 0 effects the code
            if ($newRecord === true
                && count($primaryKey) === 1 // lastInsertId is only possible for tables where there is an auto increment column which is the primary key
                && is_null($data->{$primaryKey[0]}())) { //If the primaryKey[0] is not null, this was a manual key, not auto increment
                $data = $data->setValueByKey($primaryKey[0], $connection->lastInsertId());
            }
            return $data;
        }
        $this->lastErrorInfo = array_merge(
            $this->$connection->getErrorInfo(),
            [
                'query'         => $query
            ]
        );
        return null;
    }

    // /**
    //  * Returns an INSERT query string.
    //  *
    //  * @param ArrayObject $fields       The fields to be set.
    //  * @param ArrayObject $parameters   The parameters to bind.
    //  * @param int   $insertMode   The insert mode.
    //  *
    //  * @returns string   The INSERT query string.
    //  */
    // protected function getInsertQuery(ArrayObject $fields, ArrayObject $parameters, $insertMode = DaoHelper::INSERT_MODE_NORMAL)
    // {
    //     $queryString = '
    //         INSERT ' . (($insertMode == DaoHelper::INSERT_MODE_IGNORE) ? 'IGNORE' : '') . ' INTO
    //             ' . $this->tableName . '
    //             (
    //                 `' . implode('`, `', $fields->getArrayCopy()) . '`
    //             )
    //         VALUES
    //             (
    //                 ' . implode(', ', $parameters->getArrayCopy()) . '
    //             )
    //     ';
    //     if ($insertMode == DaoHelper::INSERT_MODE_UPDATE) {
    //         $queryString .= '
    //             ON DUPLICATE KEY UPDATE
    //                 ' . This\Helper\SqlQueryHelper::createUpdateAssignments($fields, $parameters);
    //     }
    //     return $queryString;
    // }

    // /**
    //  * Returns an UPDATE query string.
    //  *
    //  * @param ArrayObject $fields       The fields to be set.
    //  * @param ArrayObject $parameters   The parameters to bind.
    //  *
    //  * @returns string   The UPDATE query string.
    //  */
    // protected function getUpdateQuery(
    //     ArrayObject $fields,
    //     ArrayObject $parameters,
    //     array $primaryKey
    // )
    // {
    //     // Primary keys can not be overwritten
    //     $fieldsToUpdate = new ArrayObject();
    //     $paramteresToUpdate = new ArrayObject();
    //     $conditions = [];

    //     $iterator = $fields->getIterator();
    //     while ($iterator->valid()) {
    //         if (in_array($iterator->current(), $primaryKey)) {
    //             // The value here does not matter as the binding is done in the save method save
    //             // It has to be some value though otherwise it will be transformed to IS NULL
    //             $conditions[$iterator->current()] = 'value';
    //         } else {
    //             $fieldsToUpdate->offsetSet(null, $iterator->current());
    //             $paramteresToUpdate->offsetSet(null, $parameters->offsetGet($iterator->key()));
    //         }

    //         $iterator->next();
    //     }
    //     return '
    //         UPDATE
    //             ' . $this->tableName . '
    //         SET
    //             ' . This\Helper\SqlQueryHelper::createUpdateAssignments($fieldsToUpdate, $paramteresToUpdate) . '
    //         ' . This\Helper\SqlQueryHelper::getWhereConditions($conditions);
    // }

    /**
     * Returns a record from the table.
     */
    public function getRecord(array $whereConditions = []) : ?\ArrayObject
    {
        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/
            SELECT
                *
            FROM
                ' . $this->tableName . '
            ' . This\Helper\SqlQueryHelper::getWhereConditions($whereConditions) . '
            LIMIT 1
        ';
        $stmt  = $this->getConnection()->prepare($query);
        foreach ($whereConditions as $fieldName => $param) {
            $param = This\Helper\SqlQueryHelper::normalizeWhereConditionParams($param);
            if (is_array($param['value'])) {
                foreach ($param['value'] as $key => $value) {
                    This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $fieldName . $key, $value);
                }
            }
        }

        $stmt->execute();
        return $this->getResultObject($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Returns the record with the previous and next record.
     *
     * @param int   $id                The id of the record which we are interested in.
     * @param array $whereConditions   Conditions to select the records.
     *
     * @returns array   Array with 3 elements. [0] - previous item, [1] - selected element, [2] - next item
     *
     * @internal We need to query exactly three records from the database to be consistent so if there isn't previous
     * or next item, we need to replace them with an empty row.
     * A subquery without the LEFT JOIN on SELECT(1) wont return any row if no matching record is found.
     * Also if we dont use the UNION ALL the duplicated rows will be merged, so in case of missing previous
     * and next record we will get only one empty row
     */
    public function getRecordWithSiblings($id, array $whereConditions = array())
    {
        $previousSiblingConditions = array_merge($whereConditions, array(
            'id' => array(
                'value'    => $id,
                'operator' => '<'
            )
        ));
        $currentConditions         = array_merge($whereConditions, array(
            'id' => $id
        ));
        $nextSiblingConditions     = array_merge($whereConditions, array(
            'id' => array(
                'value'    => $id,
                'operator' => '>'
            )
        ));

        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/
            (
                SELECT
                    ' . $this->tableName . '.*
                FROM
                    (SELECT 1) AS v
                    ' . This\Helper\SqlQueryHelper::createJoinStatement(
                        $this->tableName,
                        $previousSiblingConditions,
                        This\Helper\SqlQueryHelper::JOIN_MODE_LEFT
                    ) . '
                ORDER BY id DESC
                LIMIT 1
            )
            UNION ALL
            (
                SELECT
                    ' . $this->tableName . '.*
                FROM
                    (SELECT 1) AS v
                    ' . This\Helper\SqlQueryHelper::createJoinStatement(
                        $this->tableName,
                        $currentConditions,
                        This\Helper\SqlQueryHelper::JOIN_MODE_LEFT
                    ) . '
            )
            UNION ALL
            (
                SELECT
                    ' . $this->tableName . '.*
                FROM
                    (SELECT 1) AS v
                    ' . This\Helper\SqlQueryHelper::createJoinStatement(
                        $this->tableName,
                        $nextSiblingConditions,
                        This\Helper\SqlQueryHelper::JOIN_MODE_LEFT
                    ) . '
                ORDER BY id ASC
                LIMIT 1
            )
        ';

        $stmt = $this->getConnection()->prepare($query);
        foreach ($currentConditions as $fieldName => $param) {
            foreach ($param['value'] as $key => $value) {
                This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $fieldName . $key, $value);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function executeQuery(
        string $query,
        array $whereConditions = [],
        int $limit = 0,
        int $offset = 0
    ) : ?\ArrayObject
    {
        $stmt = $this->getConnection()->prepare($query);
        foreach ($whereConditions as $fieldName => $param) {
            $param = This\Helper\SqlQueryHelper::normalizeWhereConditionParams($param);
            if (is_array($param['value'])) {
                foreach ($param['value'] as $key => $value) {
                    This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $fieldName . $key, $value);
                }
            }
        }
        if ($limit > 0) {
            $stmt->bindValue(':_queryLimit', (int) $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':_queryOffset', (int) $offset, \PDO::PARAM_INT);
        }
// echo \Phalconeer\Dev\TVarDumper::dump([$query, $stmt, $whereConditions]);
        try {
            $stmt->execute();
        } catch (\Exception $exception) {
            throw new This\Exception\SqlExecutionException(
                $exception->getMessage() . PHP_EOL . ' -------- ' . PHP_EOL . $query . PHP_EOL . ' ++++++++ ' . PHP_EOL . json_encode($whereConditions),
                This\Helper\ExceptionHelper::MYSQL_EXECUTION_FAILED,
                $exception
            );
        }
// $result = $this->getResultObjectSet($stmt->fetchAll(\PDO::FETCH_ASSOC));
// echo \Phalconeer\Dev\TVarDumper::dump([get_class($this), $result]);
// return $result;
        return $this->getResultObjectSet($stmt->fetchAll(\PDO::FETCH_ASSOC));
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
        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/
            SELECT
                *
            FROM
                ' . $this->tableName . '
            ' . This\Helper\SqlQueryHelper::getWhereConditions($whereConditions) .
                (strlen($orderString) > 0
                        ? '
            ORDER BY ' . This\Helper\SqlQueryHelper::convertQuerySortParameters($orderString)
                        : '') .
                (($limit > 0)
                        ? '
            LIMIT :_queryLimit
            OFFSET :_queryOffset'
                        : ''
                );
// echo \Phalconeer\Dev\TVarDumper::dump([
//     $query,
//     $whereConditions,
//     $limit,
//     $offset
// ]);
        return $this->executeQuery(
            $query,
            $whereConditions,
            $limit,
            $offset
        );
    }

    /**
     * Returns the total count of specific records.
     */
    public function getCount(array $whereConditions = []) : int
    {
        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/
            SELECT
                COUNT(1)
            FROM
                ' . $this->tableName . '
            ' . This\Helper\SqlQueryHelper::getWhereConditions($whereConditions);

        $stmt = $this->getConnection()->prepare($query);
        foreach ($whereConditions as $fieldName => $param) {
            foreach ($param['value'] as $key => $value) {
                This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $fieldName . $key, $value);
            }
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Deletes a record from the table.
     */
    public function delete(array $whereConditions = []) : bool
    {
        $connection = $this->getConnection(Dao\Helper\DaoHelper::CONNECTION_TYPE_READ_WRITE);
        $query = '/*QueryID:' . static::class . '::' . __FUNCTION__ . '*/
            DELETE
            FROM
                ' . $this->tableName . '
            ' . This\Helper\SqlQueryHelper::getWhereConditions($whereConditions) . '
        ';
        $stmt  = $connection->prepare($query);
        foreach ($whereConditions as $fieldName => $param) {
            foreach ($param['value'] as $key => $value) {
                This\Helper\SqlQueryHelper::bindValue($stmt, ':' . $fieldName . $key, $value);
            }
        }

        return $stmt->execute();
    }

    /**
     * Returns information about the error.
     */
    public function getLastErrorInfo() : ?string
    {
        return $this->lastErrorInfo;
    }

    public function getTableName() : string
    {
        return $this->tableName;
    }
}
