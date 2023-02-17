<?php
namespace Phalconeer\MySqlAdapter\Test;

use Test;
use Phalconeer\MySqlAdapter;
use Phalconeer\MySqlAdapter\Test as This;
use Phalconeer\Condition;
use Phalconeer\Dto;

class SqlQueryHelperTest extends Test\UnitTestCase
{
    public function testEmptyArray()
    {
        $whereConditions = [];
        $this->assertEquals(
            '',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Empty conditions has to return an empty string'
        );
    }

    public function testSimpleCondition()
    {
        $whereConditions = [
                'id'    => 1
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Simple condition definition failed'
        );
        $this->assertEquals(
            [
                'id'    => [
                    'operator'      => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'         => [1]
                ]
            ],
            $whereConditions,
            'Where condition was not updated'
        );

        $whereConditions = [
                'id'    => 'asasasdasdasd'
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Strings are quoted only by PDO bind param'
        );
        $this->assertEquals(
            [
                'id'    => [
                    'operator'      => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'         => ['asasasdasdasd']
                ]
            ],
            $whereConditions,
            'Where condition was not updated'
        );

        $whereConditions = [
                'id'    => [
                    'value'     => 1
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Operator handling failed'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'     => 1
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Operator handling failed'
        );

        $whereConditions = [
                'id'    => [1, 2]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple values did not return IN statement'
        );

        $whereConditions = [
                'id'    => [
                    'value'     => [1, 2]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple values did not return IN statement'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_IN,
                    'value'     => [1, 2]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple values did not return IN statement'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'     => [1, 2]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple values did not return IN statement when operator is EQUAL'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_LIKE,
                    'value'     => '%TEST%'
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` LIKE :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Like operator did not return properly'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_NOT_LIKE,
                    'value'     => '%TEST%'
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` NOT LIKE :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Not like operator did not return properly'
        );
    }

    public function testFieldNameConversion()
    {
        $whereConditions = [
                'camelCaseNamesAreConverted'    => 1
        ];
        $this->assertEquals(
'
        WHERE
            `camel_case_names_are_converted` = :camel_case_names_are_converted0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Field names are not converted to underscore'
        );
    }

    public function testSimpleNegatedCondition()
    {
        $whereConditions = [
                'id'    => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL,
                    'value'     => 1
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` != :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Not equal operator failed'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_NOT_IN,
                    'value'     => [1, 2]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` NOT IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'NOT IN operator failed'
        );

        $whereConditions = [
                'id'    => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL,
                    'value'     => [1, 2]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` NOT IN (:id0, :id1)
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Failed to change NOT_EQUAL to NOT_IN'
        );
    }

    public function testNullCondition()
    {
        $whereConditions = [
                'id'    => null
            ];
        $this->assertEquals(
'
        WHERE
            `id` IS NULL 
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'IS NULL operator failed'
        );
        $whereConditions = [
                'id'    => [
                    'value'     => null
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IS NULL 
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'IS NULL operator failed'
        );
        $whereConditions = [
                'id'    => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL,
                    'value'     => null
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` IS NOT NULL 
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'IS NOT NULL operator failed'
        );
    }


    public function testRangeConditions()
    {
        $whereConditions = [
                'id'        => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_GREATER,
                    'value'     => 10
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` > :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Greater operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL,
                    'value'     => 10
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` >= :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Greater or equal operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_LESS,
                    'value'     => 10
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` < :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Less operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL,
                    'value'     => 10
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` <= :id0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Less or equal operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_BETWEEN,
                    'value'     => [10, 20]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` BETWEEN :id0 AND :id1
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Between operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_BETWEEN,
                    'value'     => [10, 20, 30]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` BETWEEN :id0 AND :id1
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Between operator failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'  => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_NOT_BETWEEN,
                    'value'     => [10, 20]
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` NOT BETWEEN :id0 AND :id1
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Not between operator failed'
        );
    }

    public function testMultipleConditions()
    {
        $whereConditions = [
                'id'        => 10,
                'type'      => 'a'
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
        AND
            `type` = :type0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple conditions failed'
        );

        $whereConditions = [
                'id'        => 10,
                'type'      => 'a'
            ];
        $this->assertEquals(
'
        WHERE
            `id` = :id0
        OR
            `type` = :type0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions, MySqlAdapter\Helper\SqlQueryHelper::LOGICAL_OPERATOR_OR),
            'Multiple conditions failed'
        );

        $whereConditions = [
                'id'        => [
                    'operator'      => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_BETWEEN,
                    'value'         => [10, 20]
                ],
                'type'      => [
                    'operator'      => Condition\Helper\ConditionHelper::OPERATOR_NOT_EQUAL,
                    'value'         => 'a',
                ]
            ];
        $this->assertEquals(
'
        WHERE
            `id` BETWEEN :id0 AND :id1
        AND
            `type` != :type0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Multiple conditions failed'
        );
    }

    public function testJoinStatement()
    {
        $whereConditions = [
                'tbl1.id'           => 10,
                'tbl2.type'         => 'a'
            ];
        $this->assertEquals(
'
        WHERE
            `tbl1`.`id` = :tbl1id0
        AND
            `tbl2`.`type` = :tbl2type0
',
            MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions),
            'Named talbe conditions failed'
        );

        $this->assertEquals(
            [
                'tbl1id'            => [
                    'operator'          => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'             => [10]
                ],
                'tbl2type'          => [
                    'operator'          => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'             => ['a']
                ]
            ],
            $whereConditions
        );

        $this->assertEquals(
'
        INNER JOIN
            `table_two`',
            MySqlAdapter\Helper\SqlQueryHelper::createJoinStatement(
                'table_two'
            ),
            'Join statement was not created correctly'
        );

        $joinConditions = [
            'tbl1.type'     => '`tbl2`.`type`'
        ];
        $this->assertEquals(
'
        INNER JOIN
            `table_two`
        ON
            `tbl1`.`type` = :tbl1type0',
            MySqlAdapter\Helper\SqlQueryHelper::createJoinStatement(
                'table_two',
                $joinConditions
            ),
            'Join statement was not created correctly'
        );
        $this->assertEquals(
            [
                'tbl1type'      => [
                    'operator'      => Condition\Helper\ConditionHelper::OPERATOR_EQUAL,
                    'value'         => [
                        '`tbl2`.`type`'
                    ]
                ]
            ],
            $joinConditions,
            'Join conditions were not updated properly'
        );

        $joinConditions = [
            'tbl1.type'     => '`tbl2`.`type`',
            'tbl1.date'     => [
                'operator'      => Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL,
                'value'         => '2000-01-01 00:00:00'
            ]
        ];
        $this->assertEquals(
'
        INNER JOIN
            `table_two`
        ON
            `tbl1`.`type` = :tbl1type0
        AND
            `tbl1`.`date` >= :tbl1date0',
            MySqlAdapter\Helper\SqlQueryHelper::createJoinStatement(
                'table_two',
                $joinConditions
            ),
            'Join statement was not created correctly'
        );

        $joinConditions = [];
        $this->assertEquals(
'
        LEFT JOIN
            `table_two`',
            MySqlAdapter\Helper\SqlQueryHelper::createJoinStatement(
                'table_two',
                $joinConditions,
                MySqlAdapter\Helper\SqlQueryHelper::JOIN_MODE_LEFT
            ),
            'Join statement was not created correctly'
        );

        $joinConditions = [];
        $this->assertEquals(
'
        INNER JOIN
            `table_two` AS tbl2',
            MySqlAdapter\Helper\SqlQueryHelper::createJoinStatement(
                'table_two',
                $joinConditions,
                MySqlAdapter\Helper\SqlQueryHelper::JOIN_MODE_INNER,
                'tbl2'
            ),
            'Join statement was not created correctly'
        );
    }

    public function testCreateFieldsAndParameters()
    {
        $data = new This\Mock\Test(new \ArrayObject([
            'stringProperty'        => 'string',
            'intProperty'           => 10,
            'floatProperty'         => 42.42,
            'boolProperty'          => false,
            'dateTimeObject'        => new \DateTime('200-01-01 00:00:00'),
        ]));

        $this->assertEquals([
            new \ArrayObject([
                'string_property',
                'int_property',
                'float_property',
                'bool_property',
                'date_time_object',
            ]),
            new \ArrayObject([
                ':stringProperty',
                ':intProperty',
                ':floatProperty',
                ':boolProperty',
                ':dateTimeObject',
            ])
        ],
        MySqlAdapter\Helper\SqlQueryHelper::createFieldsAndParameters($data, true),
        'Field and parameter list was not created currectly'
        );
    }

    public function testCreateUpdateAssignments()
    {
        $data = new This\Mock\Test(new \ArrayObject([
            'stringProperty'        => 'string',
            'intProperty'           => 10,
            'floatProperty'         => 42.42,
            'boolProperty'          => false,
            'dateTimeObject'        => new \DateTime('200-01-01 00:00:00'),
        ]));
        $data->setDirty([
            'stringProperty',
            'intProperty',
            'floatProperty',
            'boolProperty',
            'dateTimeObject',
        ]);

        [$fields, $parameters] = MySqlAdapter\Helper\SqlQueryHelper::createFieldsAndParameters($data, false);

        $this->assertEquals(
            '`string_property` = :stringProperty, `int_property` = :intProperty, `float_property` = :floatProperty, `bool_property` = :boolProperty, `date_time_object` = :dateTimeObject',
            MySqlAdapter\Helper\SqlQueryHelper::createUpdateAssignments($fields, $parameters),
            'Field and parameter list was not created currectly'
        );
    }

    public function testConvertQuerySortParameters()
    {
        $this->assertEquals(
            'id ASC, date_created DESC',
            MySqlAdapter\Helper\SqlQueryHelper::convertQuerySortParameters('id,-dateCreated'),
            'Sort parameteres were not converted properly'
        );
    }

    public function testInvalidFieldName()
    {
        $this->expectException(MySqlAdapter\Exception\InvalidFieldNameException::class);
        $this->expectExceptionCode(MySqlAdapter\Helper\ExceptionHelper::MYSQL_ADAPTER__FIELDNAME_CONTAINS_UNDERSCORE);

        $whereConditions = [
            'sql_format_field_name'     => 0
        ];
        MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions);
    }

    public function testNumericFieldName()
    {
        $this->expectException(MySqlAdapter\Exception\InvalidFieldNameException::class);
        $this->expectExceptionCode(MySqlAdapter\Helper\ExceptionHelper::MYSQL_ADAPTER__FIELDNAME_NOT_STRING);

        $whereConditions = ['asdasdasd'];
        MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions);
    }

    public function testInvalidValue()
    {
        $this->expectException(MySqlAdapter\Exception\InvalidSqlParameterException::class);
        $this->expectExceptionCode(MySqlAdapter\Helper\ExceptionHelper::MYSQL_ADAPTER__OBJECT_PASSED_AS_VALUE);

        $whereConditions = [
            'id'     => new This\Mock\Test()
        ];
        MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions);
    }

    public function testInvalidValueAsValue()
    {
        $this->expectException(MySqlAdapter\Exception\InvalidSqlParameterException::class);
        $this->expectExceptionCode(MySqlAdapter\Helper\ExceptionHelper::MYSQL_ADAPTER__OBJECT_PASSED_AS_VALUE);

        $whereConditions = [
            'id'     => [
                'value'     => new This\Mock\Test()
            ]
        ];
        MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions);
    }

    public function testNotEnoughParametersForBetween()
    {
        $this->expectException(MySqlAdapter\Exception\InvalidParameterCountException::class);
        $this->expectExceptionCode(MySqlAdapter\Helper\ExceptionHelper::MYSQL_ADAPTER__BETWEEN_REQUIRES_TWO_PARAMETERS);

        $whereConditions = [
            'id'     => [
                'operator'      => MySqlAdapter\Helper\SqlQueryHelper::OPERATOR_BETWEEN,
                'value'         => [1]
            ]
        ];
        MySqlAdapter\Helper\SqlQueryHelper::getWhereConditions($whereConditions);
    }
}