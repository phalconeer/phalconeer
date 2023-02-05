<?php
namespace Phalconeer\Condition\Helper;

use Phalconeer\Condition as This;

class ConditionHelper
{
    const NODE_VALUE = 'value';

    const NODE_OPERATOR = 'operator';

    const OPERATOR_BETWEEN = 'between';

    const OPERATOR_BEGINS_WITH = 'startsWith';

    const OPERATOR_CONTAINS = 'contains';

    const OPERATOR_ENDS_WITH = 'endsWith';

    const OPERATOR_EQUAL = '=';

    const OPERATOR_EQUAL_URL_SAFE = 'eq';

    const OPERATOR_GREATER = '>';

    const OPERATOR_GREATER_OR_EQUAL = '>=';

    const OPERATOR_GREATER_OR_EQUAL_URL_SAFE = 'gte';

    const OPERATOR_GREATER_URL_SAFE = 'gt';

    const OPERATOR_IN = 'in';

    const OPERATOR_LESS = '<';

    const OPERATOR_LESS_OR_EQUAL = '<=';

    const OPERATOR_LESS_OR_EQUAL_URL_SAFE = 'lte';

    const OPERATOR_LESS_URL_SAFE = 'lt';

    const OPERATOR_LIKE = 'like';

    const OPERATOR_NOT_BETWEEN = 'notBetween';

    const OPERATOR_NOT_EQUAL = '!=';

    const OPERATOR_NOT_EQUAL_URL_SAFE = 'neq';

    const OPERATOR_NOT_IN = 'notIn';

    const OPERATOR_NOT_LIKE = 'notLike';

    public static function validateOperator(string $operator)
    {
        switch ($operator) {
            case self::OPERATOR_BEGINS_WITH:
            case self::OPERATOR_BETWEEN:
            case self::OPERATOR_CONTAINS:
            case self::OPERATOR_ENDS_WITH:
            case self::OPERATOR_EQUAL:
            case self::OPERATOR_EQUAL_URL_SAFE:
            case self::OPERATOR_GREATER:
            case self::OPERATOR_GREATER_OR_EQUAL:
            case self::OPERATOR_GREATER_OR_EQUAL_URL_SAFE:
            case self::OPERATOR_GREATER_URL_SAFE:
            case self::OPERATOR_IN:
            case self::OPERATOR_LESS:
            case self::OPERATOR_LESS_OR_EQUAL:
            case self::OPERATOR_LESS_OR_EQUAL_URL_SAFE:
            case self::OPERATOR_LESS_URL_SAFE:
            case self::OPERATOR_LIKE:
            case self::OPERATOR_NOT_EQUAL:
            case self::OPERATOR_NOT_EQUAL_URL_SAFE:
            case self::OPERATOR_NOT_IN:
            case self::OPERATOR_NOT_LIKE:
                break;
            default:
                throw new This\Exception\MalformedConditionException('', This\Helper\ExceptionHelper::CONDITION_CHECK__INVALID_OPERATOR);
        }
    }

    public static function validateCondition($condition) : array {
        if (is_object($condition)) {
            throw new This\Exception\MalformedConditionException('', This\Helper\ExceptionHelper::CONDITION_CHECK__OBJECT_NOT_ACCEPTABLE);
        }
        if (is_array($condition)
            && array_key_exists(self::NODE_VALUE, $condition)
            && is_object($condition[self::NODE_VALUE])) {
            throw new This\Exception\MalformedConditionException('', This\Helper\ExceptionHelper::CONDITION_CHECK__VALUE_NOT_ACCEPTABLE);
        }
        
        if (is_array($condition)
            && !array_key_exists(self::NODE_VALUE, $condition)) {
        // This can happen when the condtion is sent via URL and Phalcon can not handle double array in parameters
            $valuesArray = (array_key_exists(self::NODE_OPERATOR, $condition)) 
                ? array_diff_key($condition, [self::NODE_OPERATOR => true])
                : $condition;
            ksort($valuesArray, SORT_NUMERIC);
            $condition = [
                self::NODE_VALUE        => array_values($valuesArray),
                self::NODE_OPERATOR     => (array_key_exists(self::NODE_OPERATOR, $condition))
                    ? $condition[self::NODE_OPERATOR]
                    : self::OPERATOR_EQUAL
            ];
        }

        if (!is_array($condition)) {
            return [
                self::NODE_VALUE    => [(string) $condition],
            ];
        }

        if (array_key_exists(self::NODE_VALUE, $condition)
            && !is_array($condition[self::NODE_VALUE])) {
            $condition[self::NODE_VALUE] = [$condition[self::NODE_VALUE]];
        }

        if (array_key_exists(self::NODE_OPERATOR, $condition)) {
            self::validateOperator($condition[self::NODE_OPERATOR]);
            if (!array_key_exists(self::NODE_VALUE, $condition)) {
                return [
                    self::NODE_VALUE     => null,
                    self::NODE_OPERATOR  => $condition[self::NODE_OPERATOR]
                ];
            }
        }

        return $condition;
    }

    /**
     * Makes sure that null values does not make it to the SQL statement
     */
    public static function filterEmptyConditions($value) : bool
    {
        return (is_array($value)) 
            ? ((array_key_exists(self::NODE_VALUE, $value))
                ? !is_null($value[self::NODE_VALUE])
                : count(array_filter($value)) > 0)
            : !is_null($value);
    }
}