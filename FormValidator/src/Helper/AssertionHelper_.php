<?php
namespace Phalconeer\FormValidator\Helper;

use Phalconeer\FormValidator as This;

class AssertionHelper
{

    /**
     * Assert if the given value is null
     *
     */
    public static function assertNull($value, string $message = 'Expected to be null.')
    {
        if (!is_null($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value not null
     *
     */
    public static function assertNotNull($value, string $message = 'Expected not to be null.')
    {
        if (is_null($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is empty
     *
     */
    public static function assertEmpty($value, string $message = 'Given value is not empty')
    {
        if (!empty($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is not empty
     *
     */
    public static function assertNotEmpty($value, string $message = 'Given value is empty')
    {
        if (empty($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if given parameter is int or not
     *
     */
    public static function assertInteger($intToCheck, string $message = 'Not integer given.')
    {
        if (!is_int($intToCheck)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if given parameter is string or not
     *
     */
    public static function assertString($stringToCheck, string $message = 'Not string given.')
    {
        if (!is_string($stringToCheck)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is boolean or not
     *
     */
    public static function assertBool($value, string $message = 'Not boolean given.')
    {
        if (!is_bool($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is boolean true or not
     *
     */
    public static function assertTrue($value, string $message = 'Not boolean given.')
    {
        if ($value !== true) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is boolean true or not
     *
     */
    public static function assertFalse($value, string $message = 'Not boolean given.')
    {
        if ($value !== false) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is an array
     *
     */
    public static function assertArray($value, string $message = 'Not array given.')
    {
        if (!is_array($value)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is an instance of the given class
     *
     */
    public static function assertInstanceOf(
        $value,
        string $className,
        string $message = 'Given value is not instance of class.')
    {
        if (!is_a($value, $className)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given values equals (===)
     *
     */
    public static function assertEquals(
        $value1,
        $value2,
        string $message = 'Given values not equals.')
    {
        if ($value1 !== $value2) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if value1 is greater than value2
     *
     */
    public static function assertGreaterThan(
        $value1,
        $value2,
        string $message = 'Input value fails to be greater than expected.')
    {
        if (!is_numeric($value1)) {
            $value1 = 0;
        }
        if ($value1 <= $value2) {
            throw new This\Exception\AssertException($message);
        }
        return $value1;
    }

    /**
     * Assert if value1 is greater or equal than value2
     *
     */
    public static function assertGreaterOrEqualThan(
        $value1,
        $value2,
        string $message = 'Input value fails to be greater or equal than expected.')
    {
        if (!is_numeric($value1)) {
            $value1 = 0;
        }
        if ($value1 < $value2) {
            throw new This\Exception\AssertException($message);
        }
        return $value1;
    }

    /**
     * Assert if value1 is less than value2
     *
     */
    public static function assertLessThan(
        $value1,
        $value2,
        string $message = 'Input value fails to be less than expected.')
    {
        if (!is_numeric($value1)) {
            $value1 = 0;
        }
        if ($value1 >= $value2) {
            throw new This\Exception\AssertException($message);
        }
        return $value1;
    }

    /**
     * Assert if value1 is less or equal than value2
     *
     */
    public static function assertLessOrEqualThan(
        $value1,
        $value2,
        string $message = 'Input value fails to be less than expected.')
    {
        if (!is_numeric($value1)) {
            $value1 = 0;
        }
        if ($value1 > $value2) {
            throw new This\Exception\AssertException($message);
        }
        return $value1;
    }

    /**
     * Assert if the given string's length is less than the given value.
     *
     */
    public static function assertShorterThan(
        $value1,
        int $value2,
        string $message = 'Given string too long.')
    {
        if (strlen($value1) >= $value2) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given string's length is less than the given value.
     *
     */
    public static function assertShorterOrEqualThan(
        $value1,
        int $value2,
        string $message = 'Given string too long.')
    {
        if (strlen($value1) > $value2) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given string's length is greater than the given value.
     */
    public static function assertLongerThan(
        $value1,
        int $value2,
        string $message = 'Given string too short.')
    {
        if (strlen($value1) <= $value2) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given string's length is greater than the given value.
     */
    public static function assertLongerOrEqualThan(
        $value1,
        int $value2,
        string $message = 'Given string too short.')
    {
        if (strlen($value1) < $value2) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given value is in the array
     */
    public static function assertInArray(
        $value,
        array $possibleValues,
        string $message = 'Given value is not in the array')
    {
        if (!in_array($value, $possibleValues, true)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given key exsists in the given array
     */
    public static function assertArrayKeyExists(
        $key,
        array $associativeArray,
        string $message = 'Given key not exsist.')
    {
        if (!array_key_exists($key, $associativeArray)) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Assert if the given pattern match with the given value
     *
     */
    public static function assertMatch(
        $value,
        $regexp,
        string $message = 'Regex missmatch')
    {
        if (preg_match($regexp, $value) !== 1) {
            throw new This\Exception\AssertException($message);
        }
    }

    /**
     * Checks if the given number between the given parameters
     *
     */
    public static function assertWithinBound(
        $numberToCheck,
        $maxValue = PHP_INT_MAX,
        $minValue = PHP_INT_MIN
    )
    {
        self::assertLessThan($numberToCheck, $maxValue, 'Number is out of bound');
        self::assertGreaterThan($numberToCheck, $minValue, 'Number is out of bound');
    }

    /**
     *
     * @param string $stringToCheck
     * @param int    $maxLength
     *
     * @throws Exception
     */
    public static function assertStringLength(
        $stringToCheck,
        int $maxLength,
        int $minLength = 0)
    {
        self::assertShorterThan($stringToCheck, $maxLength);
        self::assertLongerThan($stringToCheck, $minLength);
    }
}
