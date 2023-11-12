<?php
namespace Phalconeer\Dto\Test;

use Test;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class AliasExporterTest extends Test\UnitTestCase
{
    public function testAliasExporter()
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
            'externalStringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'externalStringProperty'    => 'This is a nested string',
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
        $expectedOutputConvertFalse = new \ArrayObject([
            'externalStringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'boolProperty'      => true,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => new \ArrayObject(['--', '!!']),
            'dateTimeObject'    => new \DateTime('@0'),
            'nestedObject'      => new This\Mock\TestAliasExporterConvertFalse(new \ArrayObject([
                'stringProperty'    => 'This is a nested string', // This is not exported as converted as converChildren is off
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'boolProperty'      => false,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                // 'nestedObject'      => null, // These thre commented properties are technically unset as they were not initialised
                // 'arrayObject'       => null, // They cause the Compare function to fail, as they do not return
                // 'dateTimeObject'    => null, // Eventhough NotNull exporter was not asked, the export fuinction is not run as the entire obejct is returned
            ])),
        ]);
        $expectedOutputWithMask = new \ArrayObject([
            'externalStringProperty'    => 'This is a string',
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'externalStringProperty'    => 'This is a nested string',
                'intProperty'       => 99,
                'floatProperty'     => 0.000003,
                'arrayProperty'     => [3, 4],
                'callableProperty'  => null,
                'nestedObject'      => null, 
                'arrayObject'       => null, 
                'dateTimeObject'    => null, 
            ]),
        ]);
        $dto = new This\Mock\TestAliasExporter($testData);
        $dtoConvertFalse = new This\Mock\TestAliasExporterConvertFalse($testData);
        $dtoWithMask = new This\Mock\TestAliasExporterWithMask($testData);


        $this->assertEquals(
            $expectedOutput,
            $dto->export(),
            'Alias exporter did not function well'
        );

        $this->assertEquals(
            $expectedOutputConvertFalse,
            $dtoConvertFalse->export(),
            'Alias exporter with convert children false did not function well'
        );

        $this->assertEquals(
            $expectedOutputWithMask,
            $dtoWithMask->export(),
            'Alias exporter did not mask alias pointing to null'
        );
    }

    // public function testChainedAliasLoader()
    // {
    //     $testData = new \ArrayObject([
    //         'externalStringProperty'    => 'This is a string',
    //         'externalIntProperty'       => 10,
    //         'floatProperty'     => 1.2342342,
    //         'boolProperty'      => true,
    //         'arrayProperty'     => ['a', 'b'],
    //         'callableProperty'  => function () {},
    //         'arrayObject'       => new \ArrayObject(['--', '!!']),
    //         'dateTimeObject'    => new \DateTime('@0'),
    //         'nestedObject'      => new \ArrayObject([
    //             'stringProperty'    => 'This is a nested string',
    //             'intProperty'       => 99,
    //             'floatProperty'     => 0.000003,
    //             'boolProperty'      => false,
    //             'arrayProperty'     => [3, 4],
    //         ]),
    //         'undocumented'      => '123'
    //     ]);
    //     $dto = new This\Mock\TestChainedAliasLoader($testData);

    //     $this->assertEquals(
    //         $testData['externalStringProperty'],
    //         $dto->stringProperty(),
    //         'Alias loader did not inherit from parent'
    //     );
    //     $this->assertEquals(
    //         $testData['externalIntProperty'],
    //         $dto->intProperty(),
    //         'Chained alias loader did not function well'
    //     );
    // }

    public function testAliasExporterObject()
    {
        $testData = new \ArrayObject([
            'externalStringProperty'    => 'This is a string',
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
            'stringProperty'    => null, //Export alias is setup, but externalStringProperty is not member of the Data obnject so ti will be null
            'intProperty'       => 10,
            'floatProperty'     => 1.2342342,
            'arrayProperty'     => ['a', 'b'],
            'callableProperty'  => function () {},
            'arrayObject'       => ['--', '!!'],
            'dateTimeObject'    => '1970-01-01T00:00:00+00:00',
            'nestedObject'      => new \ArrayObject([
                'externalStringProperty'    => 'This is a nested string', //This is coming from the default exporter
                'intProperty'               => 99,
                'floatProperty'             => 0.000003,
                'boolProperty'              => false, //Default exporter does not have bool disabled 
                'arrayProperty'             => [3, 4],
                'callableProperty'          => null,
                'nestedObject'              => null, 
                'arrayObject'               => null, 
                'dateTimeObject'            => null, 
            ]),
        ]);
        $aliasTransformer = new Dto\Transformer\AliasExporter([
            'externalStringProperty'    => 'stringProperty',
            'boolProperty'              => null, //hidden from output
        ]);
        $dto = new This\Mock\TestAliasExporter($testData);
        $dto = $dto->setExportTransformers([
            Dto\Helper\TraitsHelper::EXPORT_METHOD_TO_ARRAY_OBJECT, // Requires ArrayObjectExporter trait
            $aliasTransformer
        ]);

        $this->assertEquals(
            $expectedOutput,
            $dto->export(),
            'Alias exporter did not mask alias pointing to null'
        );
    }

}