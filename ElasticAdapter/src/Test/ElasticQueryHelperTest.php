<?php
namespace Phalconeer\Modul\ElasticAdapter\Test;

use Phalconeer\Module\ElasticAdapter;
use Phalconeer\Module\ElasticAdapter\Helper\ElasticQueryHelper as EQH;
use Test\UnitTestCase;

/**
 * Class UnitTest
 */
class ElasticQueryHelperTest extends UnitTestCase
{
    public function testEmptyArray()
    {
        $this->assertEquals([], EQH::buildQuery([]), 'Empty conditions has to return an empty Array');
    }

    public function testSimpleCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'term' => [
                                'id' => 1
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => 1
            ]),
            'Simple condition definition failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'term' => [
                                'id' => 1
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '=',
                    'value' => 1
                ]
            ]),
            'Simple condition with operator and value failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'terms' => [
                                'id' => [
                                    1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'IN',
                    'value' => 1
                ]
            ]),
            'Simple condition with operator and IN failed'
        );
    }

    public function testSimpleConditionWithMultipleValues()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'terms' => [
                                'id' => [
                                    1,
                                    2
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [1, 2]
            ]),
            'Simple multiple value condition definition failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'terms' => [
                                'id' => [
                                    1,
                                    2
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'value' => [1, 2]
                ]
            ]),
            'Multiple value condition definied by value key failed'
        );
    }

    public function testSimpleNegatedCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'term' => [
                                'id' => 1
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '!=',
                    'value' => 1
                ]
            ]),
            'Simple not equal condition failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'terms' => [
                                'id' => [
                                    1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'notIn',
                    'value' => 1
                ]
            ]),
            'Simple not equal condition with `notIn` failed'
        );
    }

    public function testSimpleNegatedConditionWithMultipleValues()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'terms' => [
                                'id' => [
                                    1,
                                    2
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'notIn',
                    'value' => [1, 2]
                ]
            ]),
            'Multiple value negated condition failed'
        );
    }

    public function testNotNullCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'exists' => [
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '!=',
                    'value' => NULL
                ]
            ]),
            'Not NULL failed'
        );
        
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'exists' => [
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'notIn',
                    'value' => NULL
                ]
            ]),
            'Not NULL with `notIn` operator failed'
        );
    }

    public function testNullCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'exists' => [
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'value' => NULL
                ]
            ]),
            'NULL by value failed');
        
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'exists' => [
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    =>  NULL
            ]),
            'NULL failed');
    }

    public function testMatchCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'match' => [
                                'name' => 'Test string'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'name'    => [
                    'operator' => 'like',
                    'value' => 'Test string'
                ]
            ]),
            'MATCH opeartor failed'
        );
    }

    public function testNotMatchCondition()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            'match' => [
                                'name' => 'Test string'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'name'    => [
                    'operator' => 'notLike',
                    'value' => 'Test string'
                ]
            ]),
            'MATCH opeartor failed'
        );
    }

    public function testBetweenConditions()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gte' => 1,
                                    'lte' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'between',
                    'value' => [1, 3]
                ]
            ]),
            '`between` opeartor failed'
        );

        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gt' => 1,
                                    'lt' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'between',
                    'value' => [
                        '>' => 1,
                        '<' => 3
                    ]
                ]
            ]),
            '`between` opeartor with sub operators (<, >) specified failed'
        );

        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gte' => 1,
                                    'lte' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => 'between',
                    'value' => [
                        '>=' => 1,
                        '<=' => 3
                    ]
                ]
            ]),
            '`between` operator with sub operators (<=, >=) specified failed'
        );
    }

    public function testRangeConditions()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gt' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '>',
                    'value' => 1
                ]
            ]),
            'Bigger than operator failed'
        );
        
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gte' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '>=',
                    'value' => 1
                ]
            ]),
            'Bigger than or equal operator failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'lt' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '<',
                    'value' => 1
                ]
            ]),
            'Bigger than operator failed'
        );
        
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'lte' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '<=',
                    'value' => 1
                ]
            ]),
            'Bigger than or equal operator failed'
        );
    }

    public function testAndOperatorConditions()
    {
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'term' => [
                                'id' => 1
                            ]
                        ],
                        1 => [
                            'term' => [
                                'name' => 'Test string'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => 1,
                'name'    => 'Test string'
            ]),
            'Multiple field condition failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'terms' => [
                                'id' => [1, 2]
                            ]
                        ],
                        1 => [
                            'term' => [
                                'name' => 'Test string'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [1, 2],
                'name'    => 'Test string'
            ]),
            'Multiple field with multiple values condition failed'
        );
        $this->assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            'range' => [
                                'id' => [
                                    'gt' => 1
                                ]
                            ]
                        ],
                        1 => [
                            'term' => [
                                'name' => 'Test string'
                            ]
                        ]
                    ]
                ]
            ],
            EQH::buildQuery([
                'id'    => [
                    'operator' => '>',
                    'value' => 1
                ],
                'name'    => 'Test string'
            ]),
            'Multiple field qith range condition failed'
        );
    }

    public function testInvalidOperatorNot()
    {
        $this->expectException(ElasticAdapter\Exception\InvalidConditionException::class);
        
        EQH::buildQuery([
            'id'    => [
                'operator' => 'NOT',
                'value' => 1
            ]
        ]);
    }

    // TODO: this version of code supports this notation
    // public function testInvalidOperatorNotEqualWithMultipleValues()
    // {
    //     $this->expectException(ElasticAdapter\Exception\InvalidConditionException::class);
        
    //     EQH::buildQuery([
    //         'id'    => [
    //             'operator' => '!=',
    //             'value' => [1, 2]
    //         ]
    //     ]);
    // }

    public function testInvalidOperatorExclamation()
    {
        $this->expectException(ElasticAdapter\Exception\InvalidConditionException::class);
        
        EQH::buildQuery([
            'id'    => [
                'operator' => '!',
                'value' => 1
            ]
        ]);
    }

    public function testMissingValue()
    {
        $this->expectException(ElasticAdapter\Exception\InvalidConditionException::class);
        
        EQH::buildQuery([
            'id'    => [
                'operator' => 'NOT'
            ]
        ]);
    }

}
