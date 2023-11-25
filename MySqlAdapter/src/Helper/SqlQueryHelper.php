<?php
namespace Phalconeer\MySqlAdapter\Helper;

use Phalcon\Support\Helper\Str;
use Phalconeer\Condition;
use Phalconeer\Dao;
use Phalconeer\Dto;
use Phalconeer\Data;
use Phalconeer\MySqlAdapter as This;

class SqlQueryHelper
{
    const JOIN_MODE_INNER = 'INNER';

    const JOIN_MODE_LEFT = 'LEFT';

    const JOIN_MODE_RIGHT = 'RIGHT';

    const LOGICAL_OPERATOR_AND = 'AND';

    const LOGICAL_OPERATOR_OR = 'OR';

    const OPERATOR_IN = 'IN';

    const OPERATOR_NOT_IN = 'NOT IN';

    const OPERATOR_NULL = 'IS NULL';

    const OPERATOR_NOT_NULL = 'IS NOT NULL';

    const OPERATOR_BETWEEN = 'BETWEEN';

    const OPERATOR_NOT_BETWEEN = 'NOT BETWEEN';

    const OPERATOR_LIKE = 'LIKE';

    const OPERATOR_BEGINS_WITH = 'LIKE%';

    const OPERATOR_ENDS_WITH = '%LIKE';

    const OPERATOR_CONTAINS = '%LIKE%';

    const OPERATOR_NOT_LIKE = 'NOT LIKE';

    /**
     * Creates a fieldName that can be used in the database as field name.
     */
    public static function normalizeFieldName(string $fieldName) : string
    {
        if (strpos($fieldName, '_') !== false) {
            throw new This\Exception\InvalidFieldNameException($fieldName, This\Helper\ExceptionHelper::MYSQL_ADAPTER__FIELDNAME_CONTAINS_UNDERSCORE);
        }
        else {
            $fieldName = strtolower(preg_replace('/([A-Z])/', '_\\1', $fieldName));
        }
        
        return preg_replace('/[^a-z0-9_\.]/', '', $fieldName);
    }

    /**
     * Converts the generic order strings to SQL
     */
    public static function convertQuerySortParameters(string $paramString) : string
    {
        $parameters = explode(',', $paramString);
        array_walk($parameters, 'self::convertQuerySortParameter');
        return implode(', ', $parameters);
    }
    
    /**
     * Converts one piece of the order string into SQL
     */
    public static function convertQuerySortParameter(string &$parameter) : void
    {
        if ($parameter == '') {
            return;
        }
        if (substr($parameter, 0, 1) === '-') {
            $parameter = static::normalizeFieldName(substr($parameter, 1)) . ' DESC';
        } else {
            $parameter = static::normalizeFieldName($parameter) . ' ASC';
        }
    }
    
    public static function generatePlaceholders(string $fieldName, array $values = null) : ?array
    {
        if (is_null($values)) {
            return null;
        }
        return array_map(function($valueIndex) use ($fieldName) {
            return ':' . $fieldName . $valueIndex;
        }, array_keys($values));
    }

    /**
     * Normalizes the WHERE condition params: converts value to an array, sets the default operator, etc.
     */
    public static function normalizeWhereConditionParams($params) : array
    {
        if (is_object($params)) {
            throw new This\Exception\InvalidSqlParameterException('', This\Helper\ExceptionHelper::MYSQL_ADAPTER__OBJECT_PASSED_AS_VALUE);
        }
        if (is_array($params)
            && array_key_exists('value', $params)
            && is_object($params['value'])) {
            throw new This\Exception\InvalidSqlParameterException('', This\Helper\ExceptionHelper::MYSQL_ADAPTER__OBJECT_PASSED_AS_VALUE);
        }
        
        if (is_null($params)) {
            return [
                'value'    => NULL,
                'operator' => self::OPERATOR_NULL,
            ];
        }
        

        if (!is_array($params)) {
            return [
                'value'    => [(string) $params],
                'operator' => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
            ];
        }

        if (!isset($params['operator'])
            || $params['operator'] === Condition\Helper\ConditionHelper::OPERATOR_EQUAL) {
            $value = (array_key_exists('value', $params)) ? $params['value'] : $params;
            $params = [
                'value'    => $value,
                'operator' => is_array($value) && count($value) > 1
                        ? self::OPERATOR_IN
                        : Condition\Helper\ConditionHelper::OPERATOR_EQUAL
            ];
        }
        if (is_null($params['value'])) {
            return [
                'value'    => NULL,
                'operator' => ($params['operator'] === Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL)
                        ? self::OPERATOR_NOT_NULL
                        : self::OPERATOR_NULL
            ];
        }
        if (!is_array($params['value'])) {
            $params['value'] = [$params['value']];
        }

        switch ($params['operator']) {
            case Condition\Helper\ConditionHelper::OPERATOR_LIKE:
                $params['operator'] = self::OPERATOR_LIKE;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_NOT_LIKE:
                $params['operator'] = self::OPERATOR_NOT_LIKE;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_BETWEEN:
                $params['operator'] = self::OPERATOR_BETWEEN;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_NOT_BETWEEN:
                $params['operator'] = self::OPERATOR_NOT_BETWEEN;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_IN:
                $params['operator'] = self::OPERATOR_IN;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_NOT_IN:
                $params['operator'] = self::OPERATOR_NOT_IN;
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL:
                if (is_array($params['value']) && count($params['value']) > 1) {
                    $params['operator'] = self::OPERATOR_NOT_IN;
                }
                return $params;
            case Condition\Helper\ConditionHelper::OPERATOR_BEGINS_WITH:
            case self::OPERATOR_BEGINS_WITH:
                return [
                    'value'     => array_map(function($value) { return $value . '%';}, $params['value']),
                    'operator'  => self::OPERATOR_LIKE
                ];
            case Condition\Helper\ConditionHelper::OPERATOR_ENDS_WITH:
            case self::OPERATOR_ENDS_WITH:
                return [
                    'value'     => array_map(function($value) { return '%' . $value;}, $params['value']),
                    'operator'  => self::OPERATOR_LIKE
                ];
            case Condition\Helper\ConditionHelper::OPERATOR_CONTAINS:
            case self::OPERATOR_CONTAINS:
                return [
                    'value'     => array_map(function($value) { return '%' . $value . '%';}, $params['value']),
                    'operator'  => self::OPERATOR_LIKE
                ];
        }

        return $params;
    }

    /**
     * Wrap parameters between curly braces if there are more than one values to check against (e.g. for a function).
     */
    public static function buildPlaceholderList(array $params, array $placeholders = null) : string
    {
        if (is_null($placeholders)) {
            return '';
        }
        $delimiter = ', ';
        if (in_array(
                $params['operator'],
                [
                    self::OPERATOR_BETWEEN,
                    self::OPERATOR_NOT_BETWEEN
                ]
            )
        ) {
            $delimiter = ' ' . self::LOGICAL_OPERATOR_AND . ' ';
            if (count($placeholders) < 2) {
                throw new This\Exception\InvalidParameterCountException('', This\Helper\ExceptionHelper::MYSQL_ADAPTER__BETWEEN_REQUIRES_TWO_PARAMETERS);
            }
            array_splice($placeholders, 2);
        }

        $flattenedPlaceholderList = implode($delimiter, $placeholders);

        if (count($params['value']) > 1
                && in_array($params['operator'], [
                    self::OPERATOR_IN,
                    self::OPERATOR_NOT_IN,
                ])) {
            $flattenedPlaceholderList = '(' . $flattenedPlaceholderList . ')';
        }

        return $flattenedPlaceholderList;
    }

    /**
     * Produces a MySQL safe string, by wrapping all the table and name fields in back ticks.
     */
    public static function getKeywordSafeFieldName(string $name) : string
    {
        $safeNames = array_map(function ($namePiece) {
            return '`' . $namePiece . '`';
        },
         explode('.', $name));
        return implode('.', $safeNames);
    }
    
    public static function getConditions(array &$whereConditions) : array
    {
        $conditions               = array();
        $whereConditionsConverted = array();
        
        foreach ($whereConditions as $fieldName => $params) {
            if (is_numeric($fieldName)) {
                throw new This\Exception\InvalidFieldNameException($fieldName, This\Helper\ExceptionHelper::MYSQL_ADAPTER__FIELDNAME_NOT_STRING);
            }
            $normalizedFieldName  = static::normalizeFieldName($fieldName);
            $pdoSafeFieldName = str_replace('.', '', $normalizedFieldName);
            $normalizedParameters = static::normalizeWhereConditionParams($params);
            $placeholderList      = static::generatePlaceholders($pdoSafeFieldName, $normalizedParameters['value']);

            $conditions[$fieldName] = implode(' ', array(
                static::getKeywordSafeFieldName($normalizedFieldName),
                $normalizedParameters['operator'],
                static::buildPlaceholderList($normalizedParameters, $placeholderList)
            ));

            $whereConditionsConverted[$pdoSafeFieldName] = $normalizedParameters;
        }

        $whereConditions = $whereConditionsConverted;
        return $conditions;
    }

    public static function getConditionList(
        array &$whereConditions = [],
        $logicalOperator = self::LOGICAL_OPERATOR_AND
    ) : string
    {
        return implode('
        ' . $logicalOperator . '
            ', static::getConditions($whereConditions));
    }

    /**
     * Returns the WHERE conditions string.
     */
    public static function getWhereConditions(
        array &$whereConditions = [],
        $logicalOperator = self::LOGICAL_OPERATOR_AND
    ) : string
    {
        $query = '';
        if (count($whereConditions) > 0) {
            $query .= '
        WHERE
            ' . static::getConditionList($whereConditions, $logicalOperator) . "\n";
        }
        return $query;
    }

    /**
     * Creates the list of fields and parameters to add to the statement based on the DTO's values.
     */
    public static function createFieldsAndParameters(
        Dto\ArrayObjectExporterInterface $data,
        bool $isNewrecord
    ) : array
    {
        $fields = new \ArrayObject();
        $parameters = new \ArrayObject();
        $uncamelize = new Str\Uncamelize();
        $exporetedData = $data->export();
        $dirtyFields = $data->dataMeta->dirty();

        if (!$exporetedData instanceof \IteratorAggregate) {
            throw new This\Exception\InvalidDataFormatException(
                get_class($exporetedData) . ' is not parsable for SQL query, try converting it to ArrayObject in export method',
                This\Helper\ExceptionHelper::MYSQL_ADAPTER__DATA_OBJECT_CAN_NOT_BE_PARSED
            );
        }

        $iterator = $exporetedData->getIterator();
        while ($iterator->valid()) {
            if (($isNewrecord 
                    && !is_null($iterator->current()))
                || in_array($iterator->key(), $dirtyFields)) {
                $fields->offsetSet(null, $uncamelize($iterator->key()));
                $parameters->offsetSet(null, ':' . $iterator->key());
            }
            $iterator->next();
        }

        return [$fields, $parameters];
    }

    /**
     * Creates the update string from fields and parameters
     * field1 = :field1, field2 = :field2
     */
    public static function createUpdateAssignments(
        \ArrayObject $fields,
        \ArrayObject $parameters
    ) 
    {
        $parametersIterator = $parameters->getIterator();
        $updateAssignments = array_map(function ($field) use ($parametersIterator) {
            $result = '`' . $field . '` = ' . $parametersIterator->current();
            $parametersIterator->next();
            return $result;
        }, $fields->getArrayCopy());

        return implode(', ', $updateAssignments);
    }

    /**
     * Binds a value to the statement if the parameter is present.
     */
    public static function bindValuesToStatement(
        \PDOStatement $stmt,
        \ArrayObject $data,
        \ArrayObject $parameters
    )
    {
        foreach ($parameters as $parameterName) {
            $key = substr($parameterName, 1);
            if (!$data->offsetExists($key)) {
                throw new This\Exception\RequiredParameterNotSetException(
                    $key,
                    This\Helper\ExceptionHelper::MYSQL_ADAPTER__PARAMETER_VALUE_NOT_SET
                );
            }
            self::bindValue(
                $stmt,
                $parameterName,
                $data->offsetGet($key)
            );
        }
    }

    /**
     * Performs some tests and conversions on the value then binds it to the statement.
     */
    public static function bindValue(
        \PDOStatement $statement,
        string $name,
        $value,
        int $type = \PDO::PARAM_STR
    ) : bool
    {
        return $statement->bindValue($name, $value, $type);
    }

    /**
     * Returns an INSERT query string.
     */
    public static function getInsertQuery(
        string $tableName,
        \ArrayObject $fields,
        \ArrayObject $parameters,
        $insertMode = Dao\Helper\DaoHelper::INSERT_MODE_NORMAL
    ) : string
    {
        $queryString = '
            INSERT ' . (($insertMode == Dao\Helper\DaoHelper::INSERT_MODE_IGNORE) ? 'IGNORE' : '') . ' INTO
                ' . $tableName . '
                (
                    `' . implode('`, `', $fields->getArrayCopy()) . '`
                )
            VALUES
                (
                    ' . implode(', ', $parameters->getArrayCopy()) . '
                )
        ';
        if ($insertMode == Dao\Helper\DaoHelper::INSERT_MODE_UPDATE) {
            $queryString .= '
                ON DUPLICATE KEY UPDATE
                    ' . self::createUpdateAssignments($fields, $parameters);
        }
        return $queryString;
    }


    /**
     * Returns an UPDATE query string.
     */
    public static function getUpdateQuery(
        string $tableName,
        \ArrayObject $fields,
        \ArrayObject $parameters,
        array $whereConditions
    ) : string
    {
        return '
            UPDATE
                ' . $tableName . '
            SET
                ' . self::createUpdateAssignments($fields, $parameters) . '
            ' . self::getWhereConditions($whereConditions);
    }

    public static function createJoinStatement(
        $tableName,
        array &$joinConditions = [],
        string $joinMode = self::JOIN_MODE_INNER,
        string $alias = null
    ) : string
    {
        $conditions = (count($joinConditions) === 0)
            ? ''
            : '
        ON
            ' . static::getConditionList($joinConditions);
        return '
        ' . $joinMode . ' JOIN
            `' . $tableName . '`' . (is_string($alias)
                        ? (' AS ' . $alias)
                        : '') . $conditions;
    }
}
