<?php
namespace Phalconeer\FormValidator\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Filter;

use Phalconeer\Condition;
use Phalconeer\Config;
use Phalconeer\Dev\TVarDumper;
use Phalconeer\FormValidator as This;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

/**
 *
 */
class FormValidatorBo
{
    public function __construct(
        protected This\Data\Form $form,
        protected PhalconConfig\Config $config,
        protected ?Filter\FilterInterface $filter = null,
        protected bool $strictMode = false
    )
    {
        $this->filter = (is_null($filter))
            ? (new Filter\FilterFactory())->newInstance()
            : $filter;
    }

    /**
     * Converts the given value to the given type
     */
    protected function sanitizeValue($value, $type)
    {
        switch ($type) {
            case FVH::TYPE_STRING:
            case FVH::TYPE_PHONE_NUMBER:
                return $this->filter->sanitize($value, Filter\Filter::FILTER_STRING);
            case FVH::TYPE_EMAIL;
                return $this->filter->sanitize($value, Filter\Filter::FILTER_EMAIL);
            case FVH::TYPE_DATETIME:
                return FVH::checkDateFormat(
                    $value,
                    $this->config->get('timeFormats', Config\Helper\ConfigHelper::$dummyConfig)->toArray()
                );
            case FVH::TYPE_DATE:
                return FVH::checkDateFormat(
                    $value,
                    $this->config->get('dateFormats', Config\Helper\ConfigHelper::$dummyConfig)->toArray()
                );
            case FVH::TYPE_INT:
                return (int) $value;
            case FVH::TYPE_BOOL:
                return in_array($value, ['true', true, '1', 1], true);
            case FVH::TYPE_FLOAT:
                return (float) $value;
            default:
                return $value;
        }
    }

    protected function isStrict(This\StrictableInterface $typeRule) : bool
    {
        return (!is_null($typeRule->isStrict()))
            ? $typeRule->isStrict()
            : $this->strictMode;
    }

    protected function validateType($value, This\Data\Type $typeRule)
    {
        if (!$this->isStrict($typeRule)) {
            $value = $this->sanitizeValue($value, $typeRule->value());
        }
        switch ($typeRule->value()) {
            case FVH::TYPE_BOOL:
                return FVH::boolValidator($value);
            case FVH::TYPE_DATETIME:
                return FVH::dateValidator(
                    $value,
                    $this->config->timeFormats->toArray(),
                );
            case FVH::TYPE_DATE:
                $value = FVH::dateValidator(
                    $value,
                    $this->config->dateFormats->toArray(),
                );
                if (!is_null($value)) {
                    $value->setTime(0, 0, 0); // Make sure the date is at midnight
                }
                return $value;
            case FVH::TYPE_EMAIL:
                return FVH::emailValidator($value);
            case FVH::TYPE_FLOAT:
                return FVH::floatValidator($value);
            case FVH::TYPE_INT:
                return FVH::intValidator($value);
            case FVH::TYPE_PHONE_NUMBER:
                //TODO: add verifcication logic in case it is REALLY needed
                return $value;
            case FVH::TYPE_STRING:
                return $value;
            case FVH::TYPE_FORM:
                $formClass = $typeRule->form();
                $form = new $formClass();
                if (count($value) === 0) {
                    break;
                }
                $valueArray = (array_key_exists(0, $value)) ? $value : [$value];
                $newValues = array_map(function ($valuePiece) use ($form) {
                    return (new self(
                        $form,
                        $this->config,
                        $this->filter,
                        $this->strictMode)
                    )->validate($valuePiece);
                }, $valueArray);
                $value = (array_key_exists(0, $value)) ? $newValues : $newValues[0];
                break;
            default:
                return null;
        }
        if ($this->isStrict($typeRule)) {
            $value = $this->sanitizeValue($value, $typeRule->value());
        }
        return $value;
    }

    /**
     * Validates the field's value with the given rules
     */
    protected function validateFieldWithRules(mixed $valueToValidate, This\Data\FormField $rules) : mixed
    {
        $iterator = $rules->getIterator();
        while ($iterator->valid()) {
            $current = $iterator->current();
            switch (get_class($current)) {
                case This\Data\Type::class:
                    $valueToValidate = $this->validateType($valueToValidate, $current);
                    break;
                case This\Data\MaxLength::class:
                    $valueToValidate = (strlen($valueToValidate) <= $current->value())
                        ? $valueToValidate
                        : null;
                    break;
                case This\Data\MinLength::class:
                    $valueToValidate = (strlen($valueToValidate) >= $current->value())
                        ? $valueToValidate
                        : null;
                    break;
                case This\Data\MaxValue::class:
                    $valueToValidate = ($valueToValidate < $current->value()
                        && (!$this->isStrict($current) 
                            || is_numeric($valueToValidate)))
                        ? $valueToValidate
                        : null;
                    break;
                case This\Data\MinValue::class:
                    $valueToValidate = ($valueToValidate > $current->value()
                        && (!$this->isStrict($current) 
                            || is_numeric($valueToValidate)))
                        ? $valueToValidate
                        : null;
                    break;
                case This\Data\PossibleValues::class:
                    $valueToValidate = (in_array($valueToValidate, $current->value(), true))
                        ? $valueToValidate
                        : null;
                    break;
                case This\Data\Regex::class:
                    $valueToValidate = (preg_match($current->value(), $valueToValidate) === 1)
                        ? $valueToValidate
                        : null;
            }
            if (is_null($valueToValidate)) {
                $validation = get_class($current);
                $message = ($validation === This\Data\Type::class)
                    ? 'Type validation of `' . $current->value() . '` failed'
                    : $validation . ' validation failed';
                throw new This\Exception\ValidationException(
                    $message,
                    $current->exceptionCode(),
                );
            }
            $iterator->next();
        }

        return $valueToValidate;
    }

    /**
     * Checks if the given value is a collection/array
     */
    protected function isMultipleValue($value, This\Data\FormField $rules) : bool
    {
        /**
         * @var This\Data\Type $type
         */
        $type = ($rules->offsetExists(FVH::CHECK_TYPE))
            ? $rules->offsetGet(FVH::CHECK_TYPE)
            : null;
        if (is_null($type)
            || in_array($type->value(), FVH::$canContainMultipleValues)) {
            return is_array($value);
        }
        return false;
    }

    protected function checkRequired(This\Data\Required $required)
    {
        return $required->value() === true;
    }

    /**
     * Validates the request body
     */
    public function validate(array $data) : ?\ArrayObject
    {
        return new \ArrayObject(array_reduce(
            $this->form->getKeys(),
            function ($aggregator, $currentField) use ($data) {
                /**
                 * @var This\Data\FormField $rules
                 */
                $rules = $this->form->offsetGet($currentField);

                if (!isset($data[$currentField])) {
                    /**
                     * @var This\Data\Required
                     */
                    $required = ($rules->offsetExists(This\Helper\FormValidatorHelper::CHECK_REQUIRED))
                        ? $rules->offsetGet(This\Helper\FormValidatorHelper::CHECK_REQUIRED)
                        : null;
                    if (!is_null($required)
                        && $this->checkRequired($required)) {
                        throw new This\Exception\ValidationException(
                            'Missing required value! ' . $currentField,
                            $required->exceptionCode()
                        );
                    }
                    if (array_key_exists($currentField, $data)) {
                        $aggregator[$currentField] = null;
                    }
                    return $aggregator;
                }

                $condition = null;
                /**
                 * @var This\Data\Type
                 */
                $type = ($rules->offsetExists(This\Helper\FormValidatorHelper::CHECK_TYPE))
                    ? $rules->offsetGet(This\Helper\FormValidatorHelper::CHECK_TYPE)
                    : null;
                if (!is_null($type)
                    && $type->isCondition()) {
                    $condition = Condition\Helper\ConditionHelper::validateCondition($data[$currentField]);
                    if (!is_array($condition)) {
                        throw new This\Exception\AssertException(
                            'Invalid condition format for ' . $currentField,
                            This\Helper\ExceptionHelper::FORM__INVALID_CONDITION_FORMAT
                        );
                    }
                    $data[$currentField] = $condition[Condition\Helper\ConditionHelper::NODE_VALUE];
                }

                try {
                    if ($this->isMultipleValue($data[$currentField], $rules)) {
                        $aggregator[$currentField] = array_map(
                            function ($currentIndex) use ($currentField, $data, $rules) {
                                return $this->validateFieldWithRules(
                                    $data[$currentField][$currentIndex],
                                    $rules
                                );
                            },
                            array_keys($data[$currentField])
                        );
                    }
                    else {
                        $aggregator[$currentField] = $this->validateFieldWithRules(
                            $data[$currentField],
                            $rules
                        );
                    }
                } catch (This\Exception\ValidationException $ex) {
                    $values = is_array($data[$currentField])
                        ? $data[$currentField]
                        : [$data[$currentField]];
                    throw new This\Exception\ValidationException(
                        $currentField . ': ' . $ex->getMessage() . '; value = ' . implode(', ', $values),
                        $ex->getCode(),
                    );
                }

                if (!is_null($condition)) {
                    $condition[Condition\Helper\ConditionHelper::NODE_VALUE] = $aggregator[$currentField];
                    $aggregator[$currentField] = $condition;
                }

                return $aggregator;
            },
            []
        ));
    }
}
