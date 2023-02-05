<?php
namespace Phalconeer\Data\Test;

use Test;

use Phalconeer\Data;
use Phalconeer\Data\Test as This;

class ImmutableColllectionTest extends Test\UnitTestCase
{
    public function testSettingCollectionValues()
    {
        $testData = [
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'undocumented'      => '123'
          ],
          new This\Mock\Test([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ])
        ];

        $expetedOutput = new This\Mock\TestCollection([
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ],
          [
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ]
        ]);

        $collection = new This\Mock\TestCollection($testData);

        $this->assertEquals(
          This\Mock\Test::class,
          get_class($collection->offsetGet(0))
        );

        $this->assertEquals(
          This\Mock\Test::class,
          get_class($collection->offsetGet(1))
        );

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput,
            $collection,
          ),
          'Data is matching'
        );
    }

    public function testSettingCollectionValuesWithArrayObject()
    {
        $testData = [
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'undocumented'      => '123'
          ],
          new This\Mock\Test([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ])
        ];

        $expetedOutput = new This\Mock\TestCollection([
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ],
          [
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ]
        ]);

        $collection = new This\Mock\TestCollection(null, new \ArrayObject($testData));

        $this->assertEquals(
          This\Mock\Test::class,
          get_class($collection->offsetGet(0))
        );

        $this->assertEquals(
          This\Mock\Test::class,
          get_class($collection->offsetGet(1))
        );

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput,
            $collection
          ),
          'Data is matching'
        );
    }

    public function testFilter()
    {

        $testData = [
            [
                'stringProperty'    => 'This is a string',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'undocumented'      => '123'
            ],
            [
                'stringProperty'    => 'Middle string!',
                'intProperty'       => 17,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'undocumented'      => 11111111111
            ],
            new This\Mock\Test([
                'stringProperty'    => 'Another string',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'undocumented'      => 'undocumenTED'
            ])
        ];

        $expetedOutput1 = new This\Mock\TestCollectionFilterTrait([
            [
                'stringProperty'    => 'This is a string',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ],
        ]);
        $expetedOutput2 = new This\Mock\TestCollectionFilterTrait([
            [
                'stringProperty'    => 'Middle string!',
                'intProperty'       => 17,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ],
            [
                'stringProperty'    => 'Another string',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ]
        ]);
        $expetedOutput3 = new This\Mock\TestCollectionFilterTrait([
            [
                'stringProperty'    => 'Another string',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ]
        ]);

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'intProperty'       => 10
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput1,
            $collection
          ),
          'Filtering collection with one property and one argument works'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'intProperty'       => [-5, 17],
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput2,
            $collection
          ),
          'Filtering collection with one property and two arguments works'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'intProperty'       => [-5, 17],
            'boolProperty'      => false
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput3,
            $collection
          ),
          'Filtering collection with two properties works'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'intProperty'       => [11111],
            'boolProperty'      => false
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            new This\Mock\TestCollectionFilterTrait(),
            $collection
          ),
          'Can return empty set'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88]
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            $expetedOutput3,
            $collection
          ),
          'Array filtering works for exact match'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'boolProperty'      => false,
            'arrayProperty'     => [5]
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            new This\Mock\TestCollectionFilterTrait(),
            $collection
          ),
          'Array filtering does not allow less values to match'
        );

        $collection = new This\Mock\TestCollectionFilterTrait($testData);
        $collection = $collection->filter([
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88, 111]
        ]);

        $this->assertEquals(
          true,
          Data\Helper\CompareValueHelper::hasSameData(
            new This\Mock\TestCollectionFilterTrait(),
            $collection
          ),
          'Array filtering does not allow more values to match'
        );
    }

    public function testOrder()
    {
        $testData = [
            [
                'stringProperty'    => 'This is a string',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'undocumented'      => '123'
            ],
            [
                'stringProperty'    => 'Middle string!',
                'intProperty'       => 17,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'undocumented'      => 11111111111
            ],
            new This\Mock\Test([
                'stringProperty'    => 'Another string',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'undocumented'      => 'undocumenTED'
            ])
        ];

        $expetedOutput1 = new This\Mock\TestCollectionOrderTrait([
            $testData[2],
            $testData[0],
            $testData[1],
        ]);
        $expetedOutput2 = new This\Mock\TestCollectionOrderTrait([
            $testData[1],
            $testData[0],
            $testData[2],
        ]);
        $expetedOutput3 = new This\Mock\TestCollectionOrderTrait([
            $testData[2],
            $testData[1],
            $testData[0],
        ]);

        $collection = new This\Mock\TestCollectionOrderTrait($testData);
        $orderedCollection1 = $collection->getOrdered('intProperty');

        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput1,
                $orderedCollection1,
            ),
            'Ordering collection ascending does not work'
        );

        $this->assertEquals(
            false,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput2,
                $orderedCollection1,
            ),
            'Ordering collection ascending does not change the order'
        );

        $orderedCollection2 = $collection->getOrdered('intProperty', false);
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput2,
                $orderedCollection2,
            ),
            'Ordering collection descending does not work'
        );

        $orderedCollection3 = $collection->getOrdered('stringProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput3,
                $orderedCollection3,
            ),
            'Ordering collection ascending by string does not work'
        );
    }

    public function testSort()
    {
        $testData = [
            [
                'stringProperty'    => 'This is a string',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'undocumented'      => '123'
            ],
            [
                'stringProperty'    => 'Another string',
                'intProperty'       => 10,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'undocumented'      => 11111111111
            ],
            new This\Mock\Test([
                'stringProperty'    => 'Another string',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'undocumented'      => 'undocumenTED'
            ])
        ];

        $expetedOutput1 = new This\Mock\TestCollectionOrderTrait([
            $testData[2],
            $testData[1],
            $testData[0],
        ]);
        $expetedOutput2 = new This\Mock\TestCollectionOrderTrait([
            $testData[0],
            $testData[2],
            $testData[1],
        ]);

        $collection = new This\Mock\TestCollectionOrderTrait($testData);
        $orderedCollection1 = $collection->getSorted('intProperty,-floatProperty');

        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput1,
                $orderedCollection1,
            ),
            'Sorting collection has error'
        );

        $orderedCollection2 = $collection->getSorted('-stringProperty,floatProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput2,
                $orderedCollection2,
            ),
            'Sorting collection has error'
        );
    }

    public function testGroup()
    {
        $testData = [
            [
                'stringProperty'    => 'GROUP B',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'undocumented'      => '123'
            ],
            [
                'stringProperty'    => 'GROUP A',
                'intProperty'       => 10,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'undocumented'      => 11111111111
            ],
            new This\Mock\Test([
                'stringProperty'    => 'GROUP A',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'undocumented'      => 'undocumenTED'
            ])
        ];

        $expetedOutput1 = new \ArrayObject([
            10 => new This\Mock\TestCollectionGroupTrait([
                $testData[0],
                $testData[1],
            ]),
            -5 => new This\Mock\TestCollectionGroupTrait([
                $testData[2],
            ]),
        ]);

        $expetedOutput2 = new \ArrayObject([
            'GROUP B' => new This\Mock\TestCollectionGroupTrait([
                $testData[0],
            ]),
            'GROUP A' => new This\Mock\TestCollectionGroupTrait([
                $testData[1],
                $testData[2],
            ]),
        ]);

        $collection = new This\Mock\TestCollectionGroupTrait($testData);
        
        $groupedCollection1 = $collection->getGrouped('intProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput1,
                $groupedCollection1,
            ),
            'Grouping collection failed for intProperty'
        );
        

        $groupedCollection2 = $collection->getGrouped('stringProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput2,
                $groupedCollection2,
            ),
            'Grouping collection failed for stringProperty'
        );

        $groupedCollection3 = $collection->getGrouped('arrayProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                new \ArrayObject(),
                $groupedCollection3,
            ),
            'Grouping by array gives a result'
        );
    }

    public function testMap()
    {
        $testData = [
            [
                'stringProperty'    => 'GROUP B',
                'intProperty'       => 10,
                'floatProperty'     => 1.2342342,
                'boolProperty'      => true,
                'arrayProperty'     => ['a', 'b'],
                'undocumented'      => '123'
            ],
            [
                'stringProperty'    => 'GROUP A',
                'intProperty'       => 17,
                'floatProperty'     => 999999.999,
                'boolProperty'      => true,
                'arrayProperty'     => [true, false],
                'undocumented'      => 11111111111
            ],
            new This\Mock\Test([
                'stringProperty'    => 'GROUP A',
                'intProperty'       => -5,
                'floatProperty'     => 0.00000009,
                'boolProperty'      => false,
                'arrayProperty'     => [5, 88],
                'undocumented'      => 'undocumenTED'
            ])
        ];

        $expetedOutput1 = new This\Mock\TestCollectionMapTrait([
            10 => $testData[0],
            17 => $testData[1],
            -5 => $testData[2],
        ]);

        $expetedOutput2 = new This\Mock\TestCollectionMapTrait([
            'GROUP B' => $testData[0], // Exiusiting maps are not overwritten
            'GROUP A' => $testData[2],
        ]);

        $collection = new This\Mock\TestCollectionMapTrait($testData);
        
        $mappedCollection1 = $collection->mapFieldAsKey('intProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput1,
                $mappedCollection1,
            ),
            'Mapping collection failed for intProperty'
        );
        
        $mappedCollection2 = $collection->mapFieldAsKey('stringProperty');
        $this->assertEquals(
            true,
            Data\Helper\CompareValueHelper::hasSameData(
                $expetedOutput2,
                $mappedCollection2,
            ),
            'Mapping collection failed for stringProperty'
        );
    }
}