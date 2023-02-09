<?php
namespace Phalconeer\Dto\Test;

use Test;
use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class AliasLoaderTest extends Test\UnitTestCase
{
    public function testAliasLoader()
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
        $dto = new This\Mock\TestAliasLoader($testData);

        $this->assertEquals(
            $testData['externalStringProperty'],
            $dto->stringProperty(),
            'Alias loader did not function well'
        );
    }

    public function testChainedAliasLoader()
    {
        $testData = new \ArrayObject([
            'externalStringProperty'    => 'This is a string',
            'externalIntProperty'       => 10,
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
        $dto = new This\Mock\TestChainedAliasLoader($testData);

        $this->assertEquals(
            $testData['externalStringProperty'],
            $dto->stringProperty(),
            'Alias loader did not inherit from parent'
        );
        $this->assertEquals(
            $testData['externalIntProperty'],
            $dto->intProperty(),
            'Chained alias loader did not function well'
        );
    }

    public function testAliasLoaderObject()
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
        $aliasTransformer = new Dto\Transformer\AliasLoader([
            'externalStringProperty'    => 'stringProperty',
        ]);
        $dto = This\Mock\Test::withLoadTransformers($testData, [
            $aliasTransformer
        ]);

        $this->assertEquals(
            $testData['externalStringProperty'],
            $dto->stringProperty(),
            'Alias loader did not function well'
        );
    }

}