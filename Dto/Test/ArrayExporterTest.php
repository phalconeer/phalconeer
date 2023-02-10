<?php
namespace Phalconeer\Dto\Test;

use Test;
use Phalconeer\Data;
use Phalconeer\Dto\Test as This;

class ArrayExporterTest extends Test\UnitTestCase
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
        $testData2 = new \ArrayObject([
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
        $expectedOutputConvertFalse = [
            'stringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new This\Mock\TestArrayExporterConvertFalse(new \ArrayObject([
                'stringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                // 'nestedObject'      => null, // These thre commented properties are technically unset as they were not initialised
                // 'arrayObject'       => null, // They cause the Compare function to fail, as they do not return
                // 'dateTimeObject'    => null, // Eventhough NotNull exporter was not asked, the export fuinction is not run as the entire obejct is returned
                'undocumented'      => null //Added because of ParseTypes
            ])),
            'undocumented'      => '123' //Added because of ParseTypes
        ];

        $dto = new This\Mock\TestArrayExporter($testData);
        $dtoWithoutNull = new This\Mock\TestArrayNotNullExporter($testData);
        $dtoWithoutParseTypes = new This\Mock\TestArrayExporterWithoutParseTypes($testData);

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

        $dtoConvertFalse = new This\Mock\TestArrayExporterConvertFalse($testData);

        $this->assertEquals(
            $expectedOutputConvertFalse,
            $dtoConvertFalse->export(),
            'Array export with convert false did not work'
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
          new This\Mock\TestArrayExporter(new \ArrayObject([
            'stringProperty'    => 'Another string',
            'intProperty'       => -5,
            'floatProperty'     => 0.00000009,
            'boolProperty'      => false,
            'arrayProperty'     => [5, 88],
            'undocumented'      => 'undocumenTED'
          ]))
        ]);

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