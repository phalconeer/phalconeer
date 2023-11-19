<?php
namespace Phalconeer\Data\Test;

use Test;

use Phalconeer\Data;
use Phalconeer\Data\Test as This;

class ImmutableColllectionTest extends Test\UnitTestCase
{
    public function testSettingCollectionValues()
    {
        $testData = new \ArrayObject([
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'undocumented'      => '123'
          ],
          new This\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ]))
        ]);

        $expetedOutput = new This\Mock\TestCollection(new \ArrayObject([
          new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ]),
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
        ]));

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
        $testData = new \ArrayObject([
          [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'undocumented'      => '123'
          ],
          new This\Mock\Test(new \ArrayObject([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ]))
        ]);

        $expetedOutput = new This\Mock\TestCollection(new \ArrayObject([
          new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
          ]),
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
        ]));

        $collection = new This\Mock\TestCollection(new \ArrayObject($testData));

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
}