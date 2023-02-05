<?php
namespace Phalconeer\Condition\Test;

use Phalconeer\Condition\Helper\ConditionHelper as CH;

/**
 * Class UnitTest
 */
class ConditionTest extends \Test\UnitTestCase
{
    public function testEmptyArray()
    {
        $this->assertEquals(
            [
                'value'     => [],
                'operator'  => '=',
            ],
            CH::validateCondition([]),
            'Empty conditions has to return an empty Array'
        );
    }

    public function testStringValue()
    {
        $this->assertEquals(
            [
                'value' => [
                    'test'
                ]
            ],
            CH::validateCondition('test'),
            'Flat conditions are not returned as proper condition'
        );
    }

    public function testSimpleCondition()
    {
        $this->assertEquals(
            [
                'value' => [
                    1
                ],
                'operator'  => '=',
            ],
            CH::validateCondition([
                1
            ]),
            'Simple condition definition failed'
        );
        $this->assertEquals(
            [
                'operator' => '=',
                'value' => [
                    1
                ]
            ],
            CH::validateCondition([
                'operator' => '=',
                'value' => 1
            ]),
            'Simple condition with operator and value failed'
        );
        $this->assertEquals(
            [
                'operator' => 'in',
                'value' => [
                    1
                ]
            ],
            CH::validateCondition([
                'operator' => 'in',
                'value' => 1
            ]),
            'Simple condition with operator and `in` failed'
        );
    }


    public function testSimpleConditionWithMultipleValues()
    {
        $this->assertEquals(
            [
                'value' => [
                    [1, 2]
                ],
                'operator'  => '=',
            ],
            CH::validateCondition([
                [1, 2]
            ]),
            'Simple multiple value condition definition failed'
        );
        $this->assertEquals(
            [
                'value' => [1, 2]
            ],
            CH::validateCondition([
                'value' => [1, 2]
            ]),
            'Multiple value condition definied by value key failed'
        );
    }

    public function testBetweenConditionFromUrl()
    {
        $this->assertEquals(
            [
                'value'     => [
                    '2020-02-02',
                    '2020-02-03'
                ],
                'operator'  => 'between',
            ],
            CH::validateCondition([
                '0'         => '2020-02-02',
                '1'         => '2020-02-03',
                'operator'  => 'between',
            ]),
            'Range Condition can not be sent via URL'
        );
    }
}
