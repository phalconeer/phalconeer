<?php
namespace Phalconeer\ElasticAdapter\Helper;

use Phalconeer\Condition;
use Phalconeer\ElasticAdapter as This;
use Phalconeer\ElasticAdapter\Helper\ElasticQueryBodyHelper as QH;

class ElasticQueryHelper
{
    /**
     * This is the format Elasticsearch understands for date queries.
     */
    const DEFAULT_DATE_FORMAT = 'c';
    
    /**
     * This node controls the direction of the order for the field
     */
    const QUERY_ORDER = 'order';
    
    /**
     * This node controls where the elements with that field value missing is ordered
     */
    const QUERY_ORDER_MISSING = 'missing';

    const SORT_ASC = 'asc';

    const SORT_DESC = 'desc';

    const SORT_MISSING_FIRST = '_first';

    const SORT_MISSING_LAST = '_last';

    const SORT_UNMAPPED_TYPE = 'unmapped_type';

    /**
     * Array of valid range operators.
     *
     * @var array
     */
    private static $rangeOperators = array(
        Condition\Helper\ConditionHelper::OPERATOR_LESS,
        Condition\Helper\ConditionHelper::OPERATOR_LESS_URL_SAFE,
        Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL,
        Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL_URL_SAFE,
        Condition\Helper\ConditionHelper::OPERATOR_GREATER,
        Condition\Helper\ConditionHelper::OPERATOR_GREATER_URL_SAFE,
        Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL,
        Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL_URL_SAFE,
    );

    /**
     * Elasticsearch equivalent to logical or.
     *
     * @var string
     */
    private static $logicalOr = 'should';

    /**
     * Elasticsearch equivalent to logical and.
     *
     * @var string
     */
    private static $logicalAnd = 'must';

    /**
     * Elasticsearch equivalent to logical not.
     *
     * @var string
     */
    private static $logicalNot = 'must_not';

    /**
     * Pattern to find negation operators.
     *
     * @var string
     */
    private static $negationPattern = '/^(!|not?)/';

    /**
     * Convert value into elasticsearch format.
     *
     * @param mixed $value   Any value is acceptable
     *                       but if it is array with a value key then the field will be used under the key.
     *
     * @returns mixed
     */
    private static function convertToElasticValue($value, $forceArray = false)
    {
        if (is_array($value)
                && array_key_exists(QH::QUERY_VALUE, $value)) {
            $value = $value[QH::QUERY_VALUE];
        }

        if ($value instanceof \DateTime) {
            return $value->format(self::DEFAULT_DATE_FORMAT);
        }

        if ($forceArray
            && !is_array($value)) {
            $value = [$value];
        }

        return $value;
    }

    /**
     * Converts the php range operators into elasticsearch range operators.
     *
     * @param string $rangeOperator
     *
     * @returns string
     *
     * @throws \Exception
     */
    private static function convertToElasticRangeOperator($rangeOperator)
    {
        switch ($rangeOperator) {
            case Condition\Helper\ConditionHelper::OPERATOR_LESS:
            case Condition\Helper\ConditionHelper::OPERATOR_LESS_URL_SAFE:
                return Condition\Helper\ConditionHelper::OPERATOR_LESS_URL_SAFE;
            case Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL:
            case Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL_URL_SAFE:
                return Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL_URL_SAFE;
            case Condition\Helper\ConditionHelper::OPERATOR_GREATER:
            case Condition\Helper\ConditionHelper::OPERATOR_GREATER_URL_SAFE:
                return Condition\Helper\ConditionHelper::OPERATOR_GREATER_URL_SAFE;
            case Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL:
            case Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL_URL_SAFE:
                return Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL_URL_SAFE;
            case Condition\Helper\ConditionHelper::OPERATOR_BETWEEN:
                return '';
            default:
                throw new This\Exception\InvalidRangeOperatorException($rangeOperator, This\Helper\ExceptionHelper::ELASTIC_QUERY_BUILDER__INVALID_RANGE);
        }
    }

    /**
     * Creates an Elasticsearch range condition based on the given condition.
     *
     * @param array $condition   The condition descriptor.
     *
     * @returns array
     */
    private static function getElasticRangeCondition($condition)
    {
        if ($condition[QH::QUERY_OPERATOR] === Condition\Helper\ConditionHelper::OPERATOR_BETWEEN) {
            $return = [];
            foreach ($condition[QH::QUERY_VALUE] as $operator => $value) {
                $operator = ($operator === 0) ? Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL : $operator;
                $operator = ($operator === 1) ? Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL : $operator;
                $return[self::convertToElasticRangeOperator($operator)] = self::convertToElasticValue($value);
            }
            return $return;
        }
        return array(
            self::convertToElasticRangeOperator($condition[QH::QUERY_OPERATOR]) => self::convertToElasticValue($condition[QH::QUERY_VALUE]),
        );
    }

    /**
     * Tests whether the given operator is range operator or not.
     *
     * @param string $operator   The operator to test.
     *
     * @returns bool
     */
    private static function isRangeOperator($operator)
    {
        return in_array($operator, self::$rangeOperators) || $operator === Condition\Helper\ConditionHelper::OPERATOR_BETWEEN;
    }

    /**
     * Tests whether the given condition is a negation or not.
     *
     * @param string $condition   The condition to test.
     *
     * @returns bool
     */
    private static function isNegativeCondition($condition)
    {
        if (is_null($condition)) {
            //It becomes most_not.exists
            return true;
        }
        
        if (!is_array($condition)) {// in case a date object is sent as parameter
            return false;
        }

        $negated = (isset($condition[QH::QUERY_OPERATOR]) && (bool) preg_match(self::$negationPattern, $condition[QH::QUERY_OPERATOR]));
        
        if ((is_array($condition)
                && array_key_exists(QH::QUERY_VALUE, $condition)
                && is_null($condition[QH::QUERY_VALUE]))) {
            //IS NULL -> must_not.exists, IS NOT NULL -> must.exists
            return !$negated;
        }
        
        return $negated;
    }

    /**
     * Decides wheter the field name belongs to a nested object or not.
     *
     * @param string $fieldName   The field name to check.
     *
     * @return bool
     */
    private static function isNestedField($fieldName)
    {
        return strpos($fieldName, '.') !== false && strpos($fieldName, '.') !== strpos($fieldName, '.keyword');
    }

    /**
     * Creates an Elasticsearch term level query based on the given parameters.
     *
     * @param string $fieldName   The fileld name to check.
     * @param array  $condition   The condition to apply on the field.
     *
     * @returns array
     *
     * @todo create an ElasticQuery parent class and instantiate subs automatically. Then get condition by an abstract class.
     *
     * @throws \Exception
     */
    private static function normalizeCondition($fieldName, $condition)
    {
        if (is_null($condition)) {
            return [
                QH::QUERY_EXISTS => [
                    'field' => $fieldName
                ]
            ];
        } 
        
        if (!is_array($condition)) {
            return [
                QH::QUERY_TERM => [
                    $fieldName => self::convertToElasticValue($condition)
                ]
            ];
        } 
        
        if (array_key_exists(QH::QUERY_VALUE, $condition)
                && is_null($condition[QH::QUERY_VALUE])) {
            return [
                QH::QUERY_EXISTS => [
                    'field' => $fieldName
                ]
            ];
        }

        if (!array_key_exists(QH::QUERY_OPERATOR, $condition)
            && !array_key_exists(QH::QUERY_VALUE, $condition)) {
            $condition[QH::QUERY_VALUE] = $condition;
        }

        if (!array_key_exists(QH::QUERY_OPERATOR, $condition)
            || $condition[QH::QUERY_OPERATOR] === Condition\Helper\ConditionHelper::OPERATOR_EQUAL
            || strtolower($condition[QH::QUERY_OPERATOR] === Condition\Helper\ConditionHelper::OPERATOR_EQUAL_URL_SAFE)) {
            if (!is_array($condition[QH::QUERY_VALUE])) {
                return [
                    QH::QUERY_TERM => [
                        $fieldName => self::convertToElasticValue($condition[QH::QUERY_VALUE])
                    ]
                ];
            }
            $condition[QH::QUERY_OPERATOR] = Condition\Helper\ConditionHelper::OPERATOR_IN;
        }

        $condition[QH::QUERY_OPERATOR] = strtolower($condition[QH::QUERY_OPERATOR]);

        if (array_key_exists(QH::QUERY_OPERATOR, $condition)
            && $condition[QH::QUERY_OPERATOR] === Condition\Helper\ConditionHelper::OPERATOR_IN) {
            return [
                QH::QUERY_TERMS => [
                    $fieldName => self::convertToElasticValue($condition[QH::QUERY_VALUE], true)
                ]
            ];
        }

        if (array_key_exists(QH::QUERY_OPERATOR, $condition)
            && $condition[QH::QUERY_OPERATOR] === Condition\Helper\ConditionHelper::OPERATOR_LIKE) {
            return [
                QH::QUERY_MATCH => [
                    $fieldName => self::convertToElasticValue($condition[QH::QUERY_VALUE])
                ]
            ];
        }

        if (self::isRangeOperator($condition[QH::QUERY_OPERATOR])) {
            return [
                QH::QUERY_RANGE => [
                    $fieldName => self::getElasticRangeCondition($condition)
                ]
            ];
        }

        throw new This\Exception\InvalidConditionException($fieldName . json_encode($condition), This\Helper\ExceptionHelper::ELASTIC_QUERY_BUILDER__INVALID_CONDITION);
    }

    public static function convertOperator(string $operator) : string
    {
        switch ($operator) {
            case Condition\Helper\ConditionHelper::OPERATOR_EQUAL_URL_SAFE:
                return Condition\Helper\ConditionHelper::OPERATOR_EQUAL;
            default:
                // return strtoupper(preg_replace(self::$negationPattern, '', $operator));
                return preg_replace(self::$negationPattern, '', $operator);
        }
    }

    /**
     *
     * @param array $condition
     */
    private static function normalizeOperator(&$condition)
    {
        if (is_object($condition)) {
            $condition = [
                'value' => $condition
            ];
        }
        if (isset($condition[QH::QUERY_OPERATOR])) {
            $condition[QH::QUERY_OPERATOR] = self::convertOperator($condition[QH::QUERY_OPERATOR]);
        }
        return $condition;
    }

    /**
     *
     * @param array $condition
     *
     * @TODO: check if the logic here is okay. 'filter' and 'should' are not representing logical AND / OR.
     * @returns string
     */
    private static function getLogicalOperator($condition)
    {
        if (self::isNegativeCondition($condition)) {
            return self::$logicalNot;
        }

        return self::$logicalAnd;
    }

    /**
     *
     * @param array $conditions
     *
     * @return array
     */
    private static function getGroupedFieldNames(array $conditions)
    {
        return array_reduce(array_keys($conditions), function ($groupedConditions, $fieldName) use ($conditions) {
            if (!self::isNestedField($fieldName)) {
                return array_merge_recursive($groupedConditions, array(QH::QUERY_ROOT => $fieldName));
            }
            else {
                return array_merge_recursive($groupedConditions, array(QH::QUERY_NESTED => array(
                        self::getLogicalOperator($conditions[$fieldName]) => array(
                            preg_replace('/(\.[^\.]*)$/', '', $fieldName) => array($fieldName)
                        )
                )));
            }
        }, array(
            QH::QUERY_NESTED => array(),
            QH::QUERY_ROOT   => array()
        ));
    }

    /**
     *
     * @param string $objectType
     * @param array  $conditions
     *
     * @returns array
     */
    private static function createNestedQuery($objectType, $conditions)
    {
        return array(
            QH::QUERY_NESTED => array(
                QH::QUERY_PATH => $objectType,
                QH::NODE_QUERY => self::buildNestedQuery($conditions)
            )
        );
    }

    /**
     * Creates a valid elasticsearch query based on the given condition list.
     *
     * @param array $conditions   Array of conditions to normalize.
     *
     * @returns array
     */
    private static function normalizeConditions(array $conditions = array(), $isNestedQuery = false)
    {
        $groupedConditions = $isNestedQuery
                ? array(QH::QUERY_ROOT => array_keys($conditions))
                : self::getGroupedFieldNames($conditions);

        $normalizedConditions = array_reduce(
            $groupedConditions[QH::QUERY_ROOT],
            function ($reducedFilters, $fieldName) use ($conditions) {
                return array_merge_recursive(
                    $reducedFilters,
                    [
                        self::getLogicalOperator($conditions[$fieldName]) =>
                            [
                                self::normalizeCondition($fieldName, self::normalizeOperator($conditions[$fieldName]))
                            ]
                    ]
                );
            },
            []
        );

        $nestedConditions = isset($groupedConditions[QH::QUERY_NESTED])
                ? $groupedConditions[QH::QUERY_NESTED]
                : array();

        return array_reduce(
            array_keys($nestedConditions),
            function($normalizedConditions, $logicalOperator) use ($nestedConditions, $conditions) {
                $objectTypes       = $nestedConditions[$logicalOperator];
                $extractConditions = function($extractedConditions, $fieldName) use ($conditions) {
                    return array_merge(
                        $extractedConditions,
                        [
                            $fieldName => $conditions[$fieldName]
                        ]
                    );
                };
                return array_merge_recursive(
                    $normalizedConditions,
                    [
                        //@todo move mapper function into a function builder to avoid format errors
                        $logicalOperator => array_map(
                            function($objectType) use ($objectTypes, $extractConditions) {
                                $objectConditions = array_reduce(
                                    $objectTypes[$objectType],
                                    $extractConditions,
                                    []
                                );
                                return self::createNestedQuery($objectType, $objectConditions);
                            },
                            array_keys($objectTypes)
                        )
                    ]
                );
            },
            $normalizedConditions
        );
    }

    /**
        * Builds a valid query based on the given condition list.
        *
        * @param array $searchConditions   The list of conditions. <b>keys</b>: field's name, <b>value</b>: a simple value
        *                                  or an array with <b>value</b> and <b>operator</b> keys.
        *
        * @returns array
        */
    private static function buildQueryInternal(array $searchConditions = array(), $isNestedQuery = false)
    {
        $queries = array();

        if (!empty($searchConditions)) {
            $queries [QH::QUERY_TYPE_BOOL] = self::normalizeConditions($searchConditions, $isNestedQuery);
        }
        return $queries;
    }

    /**
        * Builds a valid query based on the given condition list.
        *
        * @param array $searchConditions   The list of conditions. <b>keys</b>: field's name, <b>value</b>: a simple value
        *                                  or an array with <b>value</b> and <b>operator</b> keys.
        *
        * @returns array
        */
    public static function buildQuery(array $searchConditions = array())
    {
        return self::buildQueryInternal($searchConditions);
    }

    /**
        * Builds a valid nested query based on the given condition list.
        *
        * @param array $searchConditions   The list of conditions. <b>keys</b>: field's name, <b>value</b>: a simple value
        *                                  or an array with <b>value</b> and <b>operator</b> keys.
        *
        * @returns array
        */
    public static function buildNestedQuery(array $searchConditions = array())
    {
        return self::buildQueryInternal($searchConditions, true);
    }

    public static function buildOrderClause($orderString)
    {
        $orderSettings = !empty($orderString)
                ? explode(',', $orderString)
                : [];

        return array_map(
            function($orderSetting) {
                $pieces     = explode(' ', trim($orderSetting));
                $field = $pieces[0];
                $unmapped = $pieces[1] ?? null;
                if (substr($field, 0, 1) === '-') {
                    $field = substr($field, 1);
                    $order = self::SORT_DESC;
                } else {
                    $order = self::SORT_ASC;
                }

                $return = [
                    $field => [
                        self::QUERY_ORDER           => $order,
                        self::QUERY_ORDER_MISSING   => $order === self::SORT_DESC
                                ? self::SORT_MISSING_LAST
                                : self::SORT_MISSING_FIRST,
                    ]
                ];
                
                if (!is_null($unmapped)) { // To control how unmapped calues are treates during sort
                    $return[$field][self::SORT_UNMAPPED_TYPE] = $unmapped;
                }
                
                return $return;
            },
            $orderSettings
        );
    }

    public static function getAliasedValues(array $values, string $dtoClass)
    {
        foreach ($dtoClass::getKeyAliases() as $newField => $oldField) {
            if (array_key_exists($newField, $values)) {
                $values[$oldField] = $values[$newField];
                unset($values[$newField]);
            }
        }
        return $values;
    }
}
