<?php
namespace Phalconeer\Dto\Test;

use Test\UnitTestCase;
use Phalconeer\Data;
use Phalconeer\Dto\Test as This;

class ArrayExporterTest extends UnitTestCase
{
    public function testExport()
    {
        $testData = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => [
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
            ],
            'undocumented'      => '123'
        ];
        $testData2 = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => [
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
            ],
            'undocumented'      => '123'
        ];
        $expectedOutput = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => [
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
            ],
            'undocumented'      => '123' //Added because of ParseTypes
        ];
        $expectedOutputWithoutParseTypes = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => [
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
            ],
        ];
        $expectedOutputWithoutNull = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => [
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
            ],
            'undocumented'      => '123' //Added because of ParseTypes
        ];

        $expectedOutput2 = [
            'stringProperty'    => 'This is the exported value',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => [
                'stringProperty'    => 'This is the exported value',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'nestedObject'      => null,
                'arrayObject'       => null,
                'dateTimeObject'    => null,
                'undocumented'      => null
            ],
            'undocumented'      => '123'
        ];
        $dto = new This\Mock\TestArrayExporter($testData);
        $dtoWithoutParseTypes = new This\Mock\TestArrayExporterWithoutParseTypes($testData);
        $dtoWithoutNull = new This\Mock\TestArrayNotNullExporter($testData);

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

        $dto2 = new This\Mock\TestArrayExporterWithExportFunction($testData2);

        $this->assertEquals(
            $expectedOutput2,
            $dto2->export(),
            'Dto did not use export function'
        );
    }

    public function testCollectionExport()
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
          new This\Mock\TestArrayExporter([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ])
        ];

        $expectedOutput = [
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
            'undocumented'      => '123'
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
            'undocumented'      => 'undocumenTED'
          ]
        ];
        $dto = new This\Mock\TestArrayExporterCollection($testData);

        $this->assertEquals(
            $expectedOutput,
            $dto->export(),
            'Collection was not able to transform object back to original shape'
        );
    }
}