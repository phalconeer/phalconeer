<?php
namespace Phalconeer\Dto\Test;

use Test;
use Phalconeer\Dto\Test as This;

class ArrayObjectExporterTest extends Test\UnitTestCase
{
    public function testExport()
    {
        $testData = new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
            ]),
            'undocumented'      => '123'
        ]);
        $expectedOutput = new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
                'undocumented'      => null //Added because of ParseTypes
            ]),
            'undocumented'      => '123' //Added because of ParseTypes
        ]);
        $expectedOutputWithoutParseTypes = new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ]),
        ]);
        $expectedOutputWithoutNull = new \ArrayObject([
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
            ]),
            'undocumented'      => '123' //Added because of ParseTypes
        ]);

        $dto = new This\Mock\TestArrayObjectExporter($testData);
        $dtoWithoutNull = new This\Mock\TestArrayObjectNotNullExporter($testData);
        $dtoWithoutParseTypes = new This\Mock\TestArrayObjectExporterWithoutParseTypes($testData);

        $this->assertEquals(
            $expectedOutput,
            $dto->export(),
            'Dto was not able to transform object back to original shape'
        );
        $this->assertEquals(
            $expectedOutputWithoutNull,
            $dtoWithoutNull->export(),
            'Dto was not able to ignore nulls'
        );
        $this->assertEquals(
            $expectedOutputWithoutParseTypes,
            $dtoWithoutParseTypes->export(),
            'Dto was not able to transform object back to original shape'
        );
    }

    public function testCollectionExport()
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
          new This\Mock\TestArrayObjectExporter(new \ArrayObject([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ]))
        ]);

        $expectedOutput = new \ArrayObject([
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
            'undocumented'      => '123'
          ]),
          new \ArrayObject([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'callableProperty'  => null,
            'nestedObject'      => null,
            'arrayObject'       => null,
            'dateTimeObject'    => null,
            'undocumented'      => 'undocumenTED'
          ])
        ]);
        $dto = new This\Mock\TestArrayObjectExporterCollection($testData);

        $this->assertEquals(
            $expectedOutput,
            $dto->export(),
            'Collection was not able to transform object back to original shape'
        );
    }
}