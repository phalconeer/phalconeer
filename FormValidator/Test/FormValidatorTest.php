<?php
namespace Phalconeer\FormValidator\Test;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\Config;
use Phalconeer\FormValidator;

/**
 * Class UnitTest
 */
class FormValidatorTest extends \Test\UnitTestCase
{
    public function testCheckRequired()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'requiredField'     => 'set'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);

        $this->assertInstanceOf(
            \ArrayObject::class,
            $result,
            'Form validation does not return ArrayObject'
        );
        $this->assertEquals(
            [
                'requiredField'     => 'set'
            ],
            $result->getArrayCopy(),
            'Check required failed'
        );

        $this->assertInstanceOf(
            \ArrayObject::class,
            $strictResult,
            'Form validation does not return ArrayObject when using strict'
        );
        $this->assertEquals(
            [
                'requiredField'     => 'set'
            ],
            $strictResult->getArrayCopy(),
            'Check required failed with strict'
        );

        $input = [
            'requiredField'     => false
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'requiredField'     => false
            ],
            $result->getArrayCopy(),
            'Check required failed on false'
        );
        $this->assertEquals(
            [
                'requiredField'     => false
            ],
            $strictResult->getArrayCopy(),
            'Check required failed on false when using strict'
        );

        $input = [
            'requiredField'     => 0
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'requiredField'     => 0
            ],
            $result->getArrayCopy(),
            'Check required failed on 0'
        );
        $this->assertEquals(
            [
                'requiredField'     => 0
            ],
            $strictResult->getArrayCopy(),
            'Check required failed on 0 when using strict'
        );

        $testObject = new \ArrayObject([1,2,3]);
        $input = [
            'requiredField'     => $testObject
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'requiredField'     => $testObject
            ],
            $result->getArrayCopy(),
            'Check required failed on object'
        );
        $this->assertEquals(
            [
                'requiredField'     => $testObject
            ],
            $strictResult->getArrayCopy(),
            'Check required failed on object when using strict'
        );
    }

    public function testCheckNotRequired()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\NotRequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\NotRequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'notRequiredField'     => 'set'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'notRequiredField'     => 'set'
            ],
            $result->getArrayCopy(),
            'Check not required failed when set'
        );
        $this->assertEquals(
            [
                'notRequiredField'     => 'set'
            ],
            $strictResult->getArrayCopy(),
            'Check not required failed when set using strict mode'
        );

        $input = [];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [],
            $result->getArrayCopy(),
            'Check not required failed when not set'
        );
        $this->assertEquals(
            [],
            $strictResult->getArrayCopy(),
            'Check not required failed when not set using strict mode'
        );
    }

    public function testUndefiendFieldsAreIgnored()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\StringForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\StringForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'stringField'       => 'set',
            'ignoredField'      => 'set'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'stringField'     => 'set'
            ],
            $result->getArrayCopy(),
            'Undefined field is not ignored'
        );
        $this->assertEquals(
            [
                'stringField'     => 'set'
            ],
            $strictResult->getArrayCopy(),
            'Undefined field is not ignored using strict mode'
        );
    }

    public function testCheckRegex()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RegexForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'regexField'     => 'added'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'regexField'     => 'added'
            ],
            $result->getArrayCopy(),
            'Check regex failed'
        );
    }

    public function testCheckMinValue()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'minValueField'     => 6
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minValueField'     => 6
            ],
            $result->getArrayCopy(),
            'Check min value failed'
        );
        $this->assertEquals(
            [
                'minValueField'     => 6
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed usig strict mode'
        );

        $input = [
            'minValueField'     => '6'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minValueField'     => 6
            ],
            $result->getArrayCopy(),
            'Check min value failed when converting a string'
        );
        $this->assertEquals(
            [
                'minValueField'     => 6
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed when converting a string using strict mode'
        );

        $input = [
            'minValueField'     => [
                '6',
                10,
                11.11
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minValueField'     => [
                    6,
                    10,
                    11.11
                ]
            ],
            $result->getArrayCopy(),
            'Check min value failed with multiple values'
        );
        $this->assertEquals(
            [
                'minValueField'     => [
                    6,
                    10,
                    11.11
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed with multiple values using strict mode'
        );

        $input = [
            'minValueField'     => [
                'asdasdasd',
                10,
                11.11
            ]
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'minValueField'     => [
                    'asdasdasd',
                    10,
                    11.11
                ]
            ],
            $result->getArrayCopy(),
            'Check min value failed with multiple values when converting string'
        );
    }

    public function testCheckMaxValue()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'maxValueField'     => 2
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxValueField'     => 2
            ],
            $result->getArrayCopy(),
            'Check max value failed'
        );
        $this->assertEquals(
            [
                'maxValueField'     => 2
            ],
            $strictResult->getArrayCopy(),
            'Check max value failed usig strict mode'
        );

        $input = [
            'maxValueField'     => '2'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxValueField'     => 2
            ],
            $result->getArrayCopy(),
            'Check max value failed when converting a string'
        );
        $this->assertEquals(
            [
                'maxValueField'     => 2
            ],
            $strictResult->getArrayCopy(),
            'Check max value failed when converting a string using strict mode'
        );

        $input = [
            'maxValueField'     => [
                2,
                '0',
                false,
                -11.11
            ]
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'maxValueField'     => [
                    2,
                    0,
                    0,
                    -11.11
                ]
            ],
            $result->getArrayCopy(),
            'Check max value failed with multiple values'
        );
        $this->assertEquals(
            [
                'maxValueField'     => [
                    2,
                    0,
                    0,
                    -11.11
                ]
            ],
            $result->getArrayCopy(),
            'Check max value failed with multiple values using strict mode'
        );
    }

    public function testExtendedClassWorks()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\ExtendedMaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\ExtendedMaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'maxValueField'     => 6
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxValueField'     => 6
            ],
            $result->getArrayCopy(),
            'Check max value failed on extended class'
        );
        $this->assertEquals(
            [
                'maxValueField'     => 6
            ],
            $strictResult->getArrayCopy(),
            'Check max value on extended class failed usig strict mode'
        );
    }

    public function testCheckMinLength()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'minLengthField'     => 'ab3cd6e'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minLengthField'     => 'ab3cd6e'
            ],
            $result->getArrayCopy(),
            'Check min length failed'
        );
        $this->assertEquals(
            [
                'minLengthField'     => 'ab3cd6e'
            ],
            $strictResult->getArrayCopy(),
            'Check min length failed usig strict mode'
        );

        $input = [
            'minLengthField'     => 'ab3c'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minLengthField'     => 'ab3c'
            ],
            $result->getArrayCopy(),
            'Check min length failed when exactly at limit'
        );
        $this->assertEquals(
            [
                'minLengthField'     => 'ab3c'
            ],
            $strictResult->getArrayCopy(),
            'Check min length failed when exactly at limit usig strict mode'
        );

        $input = [
            'minLengthField'     => 1234567
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minLengthField'     => 1234567
            ],
            $result->getArrayCopy(),
            'Check min length failed for number'
        );
        $this->assertEquals(
            [
                'minLengthField'     => 1234567
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed for number'
        );

        $input = [
            'minLengthField'     => 1234.567
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minLengthField'     => 1234.567
            ],
            $result->getArrayCopy(),
            'Check min length failed for float'
        );
        $this->assertEquals(
            [
                'minLengthField'     => 1234.567
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed for float'
        );

        $input = [
            'minLengthField'     => [
                1234.567,
                'This is longer'
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'minLengthField'     => [
                    1234.567,
                    'This is longer'
                ]
            ],
            $result->getArrayCopy(),
            'Check min length failed for float'
        );
        $this->assertEquals(
            [
                'minLengthField'     => [
                    1234.567,
                    'This is longer'
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check min value failed for float'
        );
    }

    public function testCheckMaxLength()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'maxLengthField'     => 'ab3cd6'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxLengthField'     => 'ab3cd6'
            ],
            $result->getArrayCopy(),
            'Check max length failed'
        );
        $this->assertEquals(
            [
                'maxLengthField'     => 'ab3cd6'
            ],
            $strictResult->getArrayCopy(),
            'Check ,ax length failed usig strict mode'
        );

        $input = [
            'maxLengthField'     => 'ab3cd6e'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxLengthField'     => 'ab3cd6e'
            ],
            $result->getArrayCopy(),
            'Check max length failed when exactly at limit'
        );
        $this->assertEquals(
            [
                'maxLengthField'     => 'ab3cd6e'
            ],
            $strictResult->getArrayCopy(),
            'Check max length failed when exactly at limit usig strict mode'
        );

        $input = [
            'maxLengthField'     => 1234
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxLengthField'     => 1234
            ],
            $result->getArrayCopy(),
            'Check max length failed for number'
        );
        $this->assertEquals(
            [
                'maxLengthField'     => 1234
            ],
            $strictResult->getArrayCopy(),
            'Check max value failed for number'
        );

        $input = [
            'maxLengthField'     => 12.3
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxLengthField'     => 12.3
            ],
            $result->getArrayCopy(),
            'Check max length failed for float'
        );
        $this->assertEquals(
            [
                'maxLengthField'     => 12.3
            ],
            $strictResult->getArrayCopy(),
            'Check max value failed for float'
        );

        $input = [
            'maxLengthField'     => [
                1.23,
                82,
                'short'
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'maxLengthField'     => [
                    1.23,
                    82,
                    'short'
                ]
            ],
            $result->getArrayCopy(),
            'Check max length failed for float'
        );
        $this->assertEquals(
            [
                'maxLengthField'     => [
                    1.23,
                    82,
                    'short'
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check max value failed for float'
        );
    }

    public function tesxtCheckPossibleValues()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\PossibleValuesForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\PossibleValuesForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'possibleValuesField'     => 1
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'possibleValuesField'     => 1
            ],
            $result->getArrayCopy(),
            'Check possible values failed'
        );
        $this->assertEquals(
            [
                'possibleValuesField'     => 1
            ],
            $strictResult->getArrayCopy(),
            'Check possible values failed usig strict mode'
        );

        $input = [
            'possibleValuesField'     => 'a'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'possibleValuesField'     => 'a'
            ],
            $result->getArrayCopy(),
            'Check possible values failed with string'
        );
        $this->assertEquals(
            [
                'possibleValuesField'     => 'a'
            ],
            $strictResult->getArrayCopy(),
            'Check possible values failed with string usig strict mode'
        );

        $input = [
            'possibleValuesField'     => true
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'possibleValuesField'     => true
            ],
            $result->getArrayCopy(),
            'Check possible values failed with boolean'
        );
        $this->assertEquals(
            [
                'possibleValuesField'     => true
            ],
            $strictResult->getArrayCopy(),
            'Check possible values failed with boolean usig strict mode'
        );

        $input = [
            'possibleValuesField'     => -0.5
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'possibleValuesField'     => -0.5
            ],
            $result->getArrayCopy(),
            'Check possible values failed with float'
        );
        $this->assertEquals(
            [
                'possibleValuesField'     => -0.5
            ],
            $strictResult->getArrayCopy(),
            'Check possible values failed with float usig strict mode'
        );

        $input = [
            'possibleValuesField'     => [
                1,
                'a',
                true,
                -0.5
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'possibleValuesField'     => [
                    1,
                    'a',
                    true,
                    -0.5
                ]
            ],
            $result->getArrayCopy(),
            'Check possible values failed with multiple values'
        );
        $this->assertEquals(
            [
                'possibleValuesField'     => [
                    1,
                    'a',
                    true,
                    -0.5
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check possible values failed with multiple values usig strict mode'
        );
    }

    public function testCheckTypeString()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\StringForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\StringForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'stringField'     => 'set'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'stringField'     => 'set'
            ],
            $result->getArrayCopy(),
            'Check type string fails'
        );
        $this->assertEquals(
            [
                'stringField'     => 'set'
            ],
            $strictResult->getArrayCopy(),
            'Check type string fails using strict mode'
        );

        $input = [
            'stringField'     => [
                'set',
                'set',
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'stringField'     => [
                    'set',
                    'set',
                ]
            ],
            $result->getArrayCopy(),
            'Check type string fails with multiple values'
        );
        $this->assertEquals(
            [
                'stringField'     => [
                    'set',
                    'set',
                ]
            ],
            $result->getArrayCopy(),
            'Check type string fails with multiple values using strict mode'
        );
    }

    public function testCheckTypeInt()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\IntForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\IntForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'intField'     => 12
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type int fails'
        );
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $strictResult->getArrayCopy(),
            'Check type int fails using strict mode'
        );

        $input = [
            'intField'     => '12'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type int fails when converting string'
        );
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $strictResult->getArrayCopy(),
            'Check type int fails when converting string using strict mode'
        );

        $input = [
            'intField'     => 12.34
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type int fails when converting float'
        );

        $input = [
            'intField'     => '12.34'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'intField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type int fails when converting float looking string'
        );

        $input = [
            'intField'     => [
                45,
                '12',
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'intField'     => [
                    45,
                    12,
                ]
            ],
            $result->getArrayCopy(),
            'Check type int fails with multiple values'
        );
        $this->assertEquals(
            [
                'intField'     => [
                    45,
                    12,
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check type int fails with multiple values using strict mode'
        );
    }

    public function testCheckTypeFloat()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\FloatForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\FloatForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'floatField'     => 12
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type float fails with int'
        );
        $this->assertEquals(
            [
                'floatField'     => 12
            ],
            $strictResult->getArrayCopy(),
            'Check type float fails with int using strict mode'
        );

        $input = [
            'floatField'     => '12'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => 12
            ],
            $result->getArrayCopy(),
            'Check type float fails when converting string int'
        );
        $this->assertEquals(
            [
                'floatField'     => 12
            ],
            $strictResult->getArrayCopy(),
            'Check type float fails when converting string int using strict mode'
        );

        $input = [
            'floatField'     => 12.34
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => 12.34
            ],
            $result->getArrayCopy(),
            'Check type float fails'
        );
        $this->assertEquals(
            [
                'floatField'     => 12.34
            ],
            $strictResult->getArrayCopy(),
            'Check type float fails using strict mode'
        );

        $input = [
            'floatField'     => '12.34'
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => 12.34
            ],
            $result->getArrayCopy(),
            'Check type float fails when converting string'
        );
        $this->assertEquals(
            [
                'floatField'     => 12.34
            ],
            $strictResult->getArrayCopy(),
            'Check type float fails when converting string using strict mode'
        );

        $input = [
            'floatField'     => 'asdasdasdasdas'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => 0
            ],
            $result->getArrayCopy(),
            'Check type float fails when converting non-numeric string'
        );

        $input = [
            'floatField'     => [
                33.11,
                '12.34'
            ]
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'floatField'     => [
                    33.11,
                    12.34
                ]
            ],
            $result->getArrayCopy(),
            'Check type float fails when converting string'
        );
        $this->assertEquals(
            [
                'floatField'     => [
                    33.11,
                    12.34
                ]
            ],
            $result->getArrayCopy(),
            'Check type float fails when converting string using strict mode'
        );
    }

    public function testCheckTypeBool()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\BoolForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\BoolForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'boolField'     => false
        ];

        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with false'
        );
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $strictResult->getArrayCopy(),
            'Check type bool fails with false using strict mode'
        );

        $input = [
            'boolField'     => 'asdasdasdas'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with string'
        );

        $input = [
            'boolField'     => ''
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with empty string'
        );

        $input = [
            'boolField'     => 0
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with 0'
        );

        $input = [
            'boolField'     => 1
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => true
            ],
            $result->getArrayCopy(),
            'Check type bool fails with 1'
        );

        $input = [
            'boolField'     => '1'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => true
            ],
            $result->getArrayCopy(),
            'Check type bool fails with string 1'
        );

        $input = [
            'boolField'     => '0'
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with `0`'
        );

        $input = [
            'boolField'     => null
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with null'
        );

        $input = [
            'boolField'     => new \ArrayObject(['a' => 'b'])
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with object'
        );
    }

    public function testCheckTypeDate()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateForm(),
            new This\Mock\ConfigMock()
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateForm(),
            new This\Mock\ConfigMock(),
            null,
            true
        );

        $input = [
            'dateField'     => new \DateTime('2000-01-30')
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with DateTime object'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with DateTime object using strict mode'
        );

        $input = [
            'dateField'     => '2000-01-30'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with well formatted string'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with well formatted string using strict mode'
        );

        $input = [
            'dateField'     => '2000.01.30.'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with well formatted dot notation string'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with well formatted dot notation string using strict mode'
        );

        $input = [
            'dateField'     => '20000130'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with no spacing string'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with no spacing string using strict mode'
        );

        $input = [
            'dateField'     => '2000/01/30'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with slashes'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with slashes using strict mode'
        );

        $input = [
            'dateField'     => '30-01-2000'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with well formatted string'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with well formatted string using strict mode'
        );

        $input = [
            'dateField'     => '30.01.2000.'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with well formatted dot notation string'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with well formatted dot notation string using strict mode'
        );

        $input = [
            'dateField'     => '30/01/2000'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $result->getArrayCopy(),
            'Check type date fails with slashes'
        );
        $this->assertEquals(
            [
                'dateField'     => new \DateTime('2000-01-30 00:00:00')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with slashes using strict mode'
        );

        $input = [
            'dateField'     => [
                '30/01/2000',
                '2001/01/30',
                '2002.01.30.',
                '2003-01-30',
            ]
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateField'     => [
                    new \DateTime('2000-01-30 00:00:00'),
                    new \DateTime('2001-01-30 00:00:00'),
                    new \DateTime('2002-01-30 00:00:00'),
                    new \DateTime('2003-01-30 00:00:00'),
                ]
            ],
            $result->getArrayCopy(),
            'Check type date fails with multiple values'
        );
        $this->assertEquals(
            [
                'dateField'     => [
                    new \DateTime('2000-01-30 00:00:00'),
                    new \DateTime('2001-01-30 00:00:00'),
                    new \DateTime('2002-01-30 00:00:00'),
                    new \DateTime('2003-01-30 00:00:00'),
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with multiple values using strict mode'
        );
    }

    public function testCheckTypeDateTime()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateTimeForm(),
            new This\Mock\ConfigMock()
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateTimeForm(),
            new This\Mock\ConfigMock(),
            null,
            true
        );

        $input = [
            'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with DateTime object'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with DateTime object using strict mode'
        );

        $input = [
            'dateTimeField'     => '2000-01-30T00:00:00.123Z'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 00:00:00.123')
            ],
            $result->getArrayCopy(),
            'Check type date fails with default JS format'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 00:00:00.123')
            ],
            $strictResult->getArrayCopy(),
            'Check type date fails with default JS format using strict mode'
        );

        $input = [
            'dateTimeField'     => '2000-01-30 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date fails with well formatted string'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with well formatted string using strict mode'
        );

        $input = [
            'dateTimeField'     => '2000.01.30. 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with well formatted dot notation string'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with well formatted dot notation string using strict mode'
        );

        $input = [
            'dateTimeField'     => '20000130 113344'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with no spacing string'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with no spacing string using strict mode'
        );

        $input = [
            'dateTimeField'     => '2000/01/30 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with slashes'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with slashes using strict mode'
        );

        $input = [
            'dateTimeField'     => '30-01-2000 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with well formatted string'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with well formatted string using strict mode'
        );

        $input = [
            'dateTimeField'     => '30.01.2000. 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with well formatted dot notation string'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with well formatted dot notation string using strict mode'
        );

        $input = [
            'dateTimeField'     => '30/01/2000 11:33:44'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $result->getArrayCopy(),
            'Check type date time fails with slashes'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => new \DateTime('2000-01-30 11:33:44')
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with slashes using strict mode'
        );

        $input = [
            'dateTimeField'     => [
                '30/01/2000 11:33:44',
                '30-01-2000 11:34:45',
                '2000-01-30 11:35:46',
                '2000.01.30. 11:36:47'
            ]
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'dateTimeField'     => [
                    new \DateTime('2000-01-30 11:33:44'),
                    new \DateTime('2000-01-30 11:34:45'),
                    new \DateTime('2000-01-30 11:35:46'),
                    new \DateTime('2000-01-30 11:36:47'),
                ]
            ],
            $result->getArrayCopy(),
            'Check type date time fails with mutiple values'
        );
        $this->assertEquals(
            [
                'dateTimeField'     => [
                    new \DateTime('2000-01-30 11:33:44'),
                    new \DateTime('2000-01-30 11:34:45'),
                    new \DateTime('2000-01-30 11:35:46'),
                    new \DateTime('2000-01-30 11:36:47'),
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check type date time fails with mutiple values using strict mode'
        );
    }

    public function testCheckTypeEmail()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\EmailForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\EmailForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'emailField'     => 'a@b.com'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'emailField'     => 'a@b.com'
            ],
            $result->getArrayCopy(),
            'Check type email fails with correct email'
        );
        $this->assertEquals(
            [
                'emailField'     => 'a@b.com'
            ],
            $strictResult->getArrayCopy(),
            'Check type email fails with correct email using strict mode'
        );

        $input = [
            'emailField'     => 'a+tag@b.com'
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'emailField'     => 'a+tag@b.com'
            ],
            $result->getArrayCopy(),
            'Check type email fails with correct email having a tag'
        );
        $this->assertEquals(
            [
                'emailField'     => 'a+tag@b.com'
            ],
            $strictResult->getArrayCopy(),
            'Check type email fails with correct email having a tag using strict mode'
        );

        $input = [
            'emailField'     => 'a(masked)@b\a.com'
        ];
        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'emailField'     => 'amasked@ba.com'
            ],
            $result->getArrayCopy(),
            'Check type email with extra characters fails with correct email'
        );

        $input = [
            'emailField'     => [
                'b(at)@c.com',
                'a+tag@b.com'
            ]
        ];
        $strictInput = [
            'emailField'     => [
                'bat@c.com',
                'a+tag@b.com'
            ]
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($strictInput);
        $this->assertEquals(
            [
                'emailField'     => [
                    'bat@c.com',
                    'a+tag@b.com'
                ]
            ],
            $result->getArrayCopy(),
            'Check type email time fails with multiple correct email'
        );
        $this->assertEquals(
            [
                'emailField'     => [
                    'bat@c.com',
                    'a+tag@b.com'
                ]
            ],
            $strictResult->getArrayCopy(),
            'Check type email fails with multiple correct email using strict mode'
        );
    }

    public function testCheckTypeForm()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\SubForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\SubForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'subForm'     => [
                'requiredField' => 'set'
            ]
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'subForm'     => new \ArrayObject([
                    'requiredField' => 'set'
                ])
            ],
            $result->getArrayCopy(),
            'Check type form fails with correct form'
        );
        $this->assertEquals(
            [
                'subForm'     => new \ArrayObject([
                    'requiredField' => 'set'
                ])
            ],
            $strictResult->getArrayCopy(),
            'Check type form fails with correct form using strict mode'
        );

        $input = [
            'subForm'     => [
                'requiredField' => 'set',
                'ignoredField'  => 'asd'
            ]
        ];
        $result = $bo->validate($input);
        $strictResult = $strictBo->validate($input);
        $this->assertEquals(
            [
                'subForm'     => new \ArrayObject([
                    'requiredField' => 'set'
                ])
            ],
            $result->getArrayCopy(),
            'Check type form fails ignoring undefined field with correct form'
        );
        $this->assertEquals(
            [
                'subForm'     => new \ArrayObject([
                    'requiredField' => 'set'
                ])
            ],
            $strictResult->getArrayCopy(),
            'Check type form fails ignoring undefined field with correct form using strict mode'
        );
    }

    public function testLocalStrictModeDisabled()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\LocalStrictModeDisabledForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'boolField'     => 0
        ];

        $result = $strictBo->validate($input);
        $this->assertEquals(
            [
                'boolField'     => false
            ],
            $result->getArrayCopy(),
            'Check type bool fails with 0 when strict mode is locally disabled'
        );
    }

    public function testRequiredFieldMissing()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::REQUIRED_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedRegex()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RegexForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'regexField'     => 'f'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::REGEX_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMinValue()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'minValueField'     => 2
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMinValueOnEquality()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'minValueField'     => 4
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMinValueConvertingStringUsingStrictMode()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'minValueField'     => 'asdasd'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMaxValue()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'maxValueField'     => 7
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MAX_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMaxValueOnEquality()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'maxValueField'     => 4
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MAX_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMaxValueOnNonNumericValue()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'maxValueField'     => 'sdfsdfsdf'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MAX_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testMaxValueFailOnExtendedClass()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $extendedBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\ExtendedMaxValueForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $input = [
            'maxValueField'     => 2
        ];

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'maxValueField'     => 2
            ],
            $result->getArrayCopy(),
            'Check max value failed'
        );
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MIN_VALUE_EXCEPTION);
        $extendedBo->validate($input);
    }

    public function testFailedMinLength()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MinLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'minLengthField'     => 'ab'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MIN_LENGTH_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedMaxLength()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\MaxLengthForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'maxLengthField'     => 'ab3cd6ef9'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::MAX_LENGTH_EXCEPTION);

        $bo->validate($input);
    }

    public function testFailedPossibleValues()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\PossibleValuesForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'possibleValuesField'     => 'ab3cd6ef9'
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::POSSIBLE_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testPartialFailedPossibleValues()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\PossibleValuesForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'possibleValuesField'     => [
                1,
                'ab3cd6ef9'
            ]
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::POSSIBLE_VALUE_EXCEPTION);

        $bo->validate($input);
    }

    public function testRequiredFieldValueMissing()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\RequiredForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'requiredField'     => null
        ];
        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::REQUIRED_EXCEPTION);

        $bo->validate($input);
    }

    public function testFloatToIntConversionFails()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\IntForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'intField'     => 12.34
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::INT_EXCEPTION);

        $strictBo->validate($input);
    }

    public function testPartialFailure()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\IntForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'intField'     => [
                1,
                3,
                5,
                12.34,
                8
            ]
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::INT_EXCEPTION);

        $strictBo->validate($input);
    }

    public function testStringToFloatConversionFails()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\FloatForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'floatField'     => 'asdasdasdasdas'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::FLOAT_EXCEPTION);

        $strictBo->validate($input);
    }

    public function testStringToBoolConversionFails()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\BoolForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'boolField'     => 'asdasdasdas'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::BOOL_EXCEPTION);

        $strictBo->validate($input);
    }

    public function testIntToBoolConversionFails()
    {
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\BoolForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'boolField'     => 0
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::BOOL_EXCEPTION);

        $strictBo->validate($input);
    }

    public function testLocalStrictModeEnabled()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\LocalStrictModeEnabledForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );

        $input = [
            'boolField'     => 0
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::BOOL_EXCEPTION);

        $bo->validate($input);
    }

    public function testInvalidDateFormat()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateForm(),
            new This\Mock\ConfigMock()
        );

        $input = [
            'dateField'     => 0
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::DATE_EXCEPTION);

        $bo->validate($input);
    }

    public function testUnusedDateFormat()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateForm(),
            new This\Mock\ConfigMock()
        );

        $input = [
            'dateField'     => '30012000'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::DATE_EXCEPTION);

        $bo->validate($input);
    }

    public function testInvalidDateTimeFormat()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateTimeForm(),
            new This\Mock\ConfigMock()
        );

        $input = [
            'dateTimeField'     => 0
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::DATE_TIME_EXCEPTION);

        $bo->validate($input);
    }

    public function testUnusedFormat()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\DateTimeForm(),
            new This\Mock\ConfigMock()
        );

        $input = [
            'dateTimeField'     => '30/12/2001 12:60:00'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::DATE_TIME_EXCEPTION);

        $bo->validate($input);
    }

    public function testInvalidEmail()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\EmailForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
        );

        $input = [
            'emailField'     => 'this is not an email'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::EMAIL_EXCEPTION);

        $bo->validate($input);
    }

    public function testExtraCharactersInEmailUsingStrictMode()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\EmailForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'emailField'     => 'a(masked)@b\a.com'
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::EMAIL_EXCEPTION);

        $bo->validate($input);
    }

    public function testStrictModeIsInherited()
    {
        $bo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\SubForm(),
            Config\Helper\ConfigHelper::$dummyConfig
        );
        $strictBo = new FormValidator\Bo\FormValidatorBo(
            new This\Form\SubForm(),
            Config\Helper\ConfigHelper::$dummyConfig,
            null,
            true
        );

        $input = [
            'subForm'     => [
                'requiredField' => 'set'
            ],
            'subFormInt'     => [
                'intField' => 12.34
            ],
        ];

        $this->expectException(FormValidator\Exception\ValidationException::class);
        $this->expectExceptionCode(This\Helper\ExceptionHelper::INT_EXCEPTION);

        $result = $bo->validate($input);
        $this->assertEquals(
            [
                'subForm'     => new \ArrayObject([
                    'requiredField' => 'set'
                ]),
                'subFormInt'     => new \ArrayObject([
                    'intField' => 12
                ])
            ],
            $result->getArrayCopy(),
            'Check type form fails with correct form'
        );

        $strictBo->validate($input);
    }
}
