<?php
namespace Phalconeer\FormValidator\Helper;

use Phalcon\Filter;
use Phalconeer\FormValidator as This;

class FormValidatorHelper
{
    const ACTION_SEARCH = 'search';

    const ACTION_CREATE = 'create';

    const ACTION_UPDATE = 'update';

    const ACTION_DELETE = 'delete';

    const KEY_CONDITION_ENABLED = 'conditionEnabled';

    const KEY_EXCEPTION_CODE = 'exceptionCode';

    const KEY_FORM = 'form';

    const KEY_STRICT = 'isStrict';

    const KEY_VALUE = 'value';

    const CHECK_REQUIRED = 'required';

    const CHECK_TYPE = 'type';

    const CHECK_REGEXP = 'regexp';

    const CHECK_MIN_VALUE = 'minValue';

    const CHECK_MAX_VALUE = 'maxValue';

    const CHECK_MIN_LENGTH = 'minLength';

    const CHECK_MAX_LENGTH = 'maxLength';

    const CHECK_POSSIBLE_VALUES = 'possibleValues';

    const TYPE_STRING = 'string';

    const TYPE_INT = 'int';

    const TYPE_FLOAT = 'float';

    const TYPE_BOOL = 'bool';

    const TYPE_DATE = 'date';

    const TYPE_DATETIME = 'datetime';

    const TYPE_EMAIL = 'email';

    const TYPE_FORM = 'form';

    const TYPE_PHONE_NUMBER = 'phoneNumber';


    public static $canContainMultipleValues = [
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
        self::TYPE_DATETIME,
        self::TYPE_DATE,
        self::TYPE_BOOL,
        self::TYPE_PHONE_NUMBER,
        self::TYPE_EMAIL
    ];

    public static function getJsonApiData(string $data)
    {
        $postData = json_decode($data, 1);
        if (is_null($postData)
            || !array_key_exists('data', $postData)
            || !array_key_exists('attributes', $postData['data'])) {
            throw new This\Exception\MalformedJSONApiRequestException('', This\Helper\ExceptionHelper::FORM__INVALID_JSON_API);
        }

        return $postData['data']['attributes'];
    }

    public static function boolValidator($value) : ?bool
    {
        return (in_array($value, [true, false], true))
            ? $value
            : null;
    }

    public static function checkDateFormat(
        $value,
        array $allowedFormats = ['Y-m-d']
    )
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        return array_reduce(
            $allowedFormats,
            function ($aggregator, $currentFormat) use ($value) {
                if ($aggregator instanceof \DateTime) {
                    return $aggregator;
                }
                $date = \DateTime::createFromFormat($currentFormat, $value);
                $errors = \DateTime::getLastErrors();
                if ($errors["warning_count"] == 0
                    && $errors["error_count"] == 0) {
                    $aggregator = $date;
                }
                return $aggregator;
            },
            $value
        );
    }

    public static function dateValidator(
        $value,
        array $allowedFormats = ['Y-m-d']
    ) : ?\DateTime
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        $value = static::checkDateFormat(
            $value,
            $allowedFormats
        );
        if (!is_a($value, \DateTime::class)) {
            return null;
        }
        return $value;
    }

    public static function emailValidator($value) : ?string
    {
        $validation = new Filter\Validation();

        $errors = $validation->add(static::TYPE_EMAIL, new Filter\Validation\Validator\Email())
            ->validate([static::TYPE_EMAIL => $value]);
        return ($errors->count() === 0)
            ? $value
            : null;
    }

    public static function floatValidator($value) : ?float
    {
        $floatValue = (float) $value;
        return ((string) $floatValue === (string) $value)
            ? $floatValue
            : null;
    }

    public static function intValidator($value) : ?int
    {
        $intValue = (int) $value;
        return ((string) $intValue === (string) $value)
            ? $intValue
            : null;
    }

}